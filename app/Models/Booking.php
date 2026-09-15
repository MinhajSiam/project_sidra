<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Booking {
    public static function all(array $filters = []): array {
        $sql = "SELECT b.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                       e.title as event_title, e.event_date, e.banner_image as event_banner,
                       p.payment_method, p.transaction_id, p.status as payment_status,
                       (SELECT COALESCE(SUM(quantity), 0) FROM booking_items WHERE booking_id = b.id) as total_tickets
                FROM `bookings` b
                JOIN `customers` c ON b.customer_id = c.id
                JOIN `events` e ON b.event_id = e.id
                LEFT JOIN `payments` p ON b.id = p.booking_id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND b.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['event_id'])) {
            $sql .= " AND b.event_id = :event_id";
            $params['event_id'] = (int)$filters['event_id'];
        }

        if (!empty($filters['customer_id'])) {
            $sql .= " AND b.customer_id = :customer_id";
            $params['customer_id'] = (int)$filters['customer_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (b.booking_reference LIKE :search OR c.name LIKE :search OR c.phone LIKE :search OR p.transaction_id LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY b.id DESC";

        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)($filters['offset'] ?? 0);
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array {
        $booking = Database::fetch(
            "SELECT b.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                    e.title as event_title, e.slug as event_slug, e.event_date, e.start_time, e.end_time,
                    v.name as venue_name, v.address as venue_address, v.city as venue_city
             FROM `bookings` b
             JOIN `customers` c ON b.customer_id = c.id
             JOIN `events` e ON b.event_id = e.id
             JOIN `venues` v ON e.venue_id = v.id
             WHERE b.id = :id LIMIT 1",
            ['id' => $id]
        );

        if ($booking) {
            $booking['items'] = self::getItems((int)$booking['id']);
            $booking['payment'] = Payment::findByBookingId((int)$booking['id']);
            $booking['tickets'] = Ticket::getByBookingId((int)$booking['id']);
        }

        return $booking;
    }

    public static function findByReference(string $ref): ?array {
        $row = Database::fetch("SELECT id FROM `bookings` WHERE booking_reference = :ref LIMIT 1", ['ref' => trim($ref)]);
        return $row ? self::find((int)$row['id']) : null;
    }

    public static function getItems(int $bookingId): array {
        return Database::fetchAll(
            "SELECT bi.*, tt.name as ticket_name, tt.description as ticket_description
             FROM `booking_items` bi
             JOIN `ticket_types` tt ON bi.ticket_type_id = tt.id
             WHERE bi.booking_id = :booking_id",
            ['booking_id' => $bookingId]
        );
    }

    public static function create(array $data, array $items): int {
        return Database::transaction(function () use ($data, $items) {
            $bookingId = Database::insert('bookings', [
                'booking_reference' => self::generateReference(),
                'customer_id' => (int)$data['customer_id'],
                'event_id' => (int)$data['event_id'],
                'total_amount' => (float)$data['total_amount'],
                'discount_amount' => (float)($data['discount_amount'] ?? 0.00),
                'final_amount' => (float)$data['final_amount'],
                'status' => $data['status'] ?? 'pending_payment',
                'notes' => $data['notes'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($items as $item) {
                Database::insert('booking_items', [
                    'booking_id' => $bookingId,
                    'ticket_type_id' => (int)$item['ticket_type_id'],
                    'quantity' => (int)$item['quantity'],
                    'unit_price' => (float)$item['unit_price'],
                    'subtotal' => (float)$item['subtotal'],
                ]);
            }

            return $bookingId;
        });
    }

    public static function updateStatus(int $id, string $status): int {
        return Database::update('bookings', [
            'status' => $status,
        ], 'id = :id', ['id' => $id]);
    }

    private static function generateReference(): string {
        $prefix = 'SDR-BK-' . date('Ym') . '-';
        do {
            $ref = $prefix . strtoupper(bin2hex(random_bytes(3)));
            $exists = Database::fetch("SELECT id FROM `bookings` WHERE booking_reference = :ref", ['ref' => $ref]);
        } while ($exists);
        return $ref;
    }
}
