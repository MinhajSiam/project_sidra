<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Payment {
    public static function all(array $filters = []): array {
        $sql = "SELECT p.*, b.booking_reference, b.final_amount as booking_total,
                       c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                       e.title as event_title,
                       u.name as reviewer_name
                FROM `payments` p
                JOIN `bookings` b ON p.booking_id = b.id
                JOIN `customers` c ON p.customer_id = c.id
                JOIN `events` e ON b.event_id = e.id
                LEFT JOIN `users` u ON p.reviewed_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['payment_method'])) {
            $sql .= " AND p.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.transaction_id LIKE :search OR p.sender_number LIKE :search OR b.booking_reference LIKE :search OR c.name LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY p.id DESC";

        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)($filters['offset'] ?? 0);
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array {
        return Database::fetch(
            "SELECT p.*, b.booking_reference, b.final_amount as booking_total,
                    c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                    e.title as event_title, e.event_date,
                    u.name as reviewer_name
             FROM `payments` p
             JOIN `bookings` b ON p.booking_id = b.id
             JOIN `customers` c ON p.customer_id = c.id
             JOIN `events` e ON b.event_id = e.id
             LEFT JOIN `users` u ON p.reviewed_by = u.id
             WHERE p.id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function findByBookingId(int $bookingId): ?array {
        return Database::fetch("SELECT * FROM `payments` WHERE booking_id = :booking_id ORDER BY id DESC LIMIT 1", [
            'booking_id' => $bookingId,
        ]);
    }

    public static function existsTrxId(string $method, string $trxId, ?int $ignorePaymentId = null): bool {
        $sql = "SELECT id FROM `payments` WHERE payment_method = :method AND transaction_id = :trxId";
        $params = [
            'method' => strtolower(trim($method)),
            'trxId' => strtoupper(trim($trxId)),
        ];
        if ($ignorePaymentId !== null) {
            $sql .= " AND id != :ignore_id";
            $params['ignore_id'] = $ignorePaymentId;
        }
        $row = Database::fetch($sql, $params);
        return $row !== null;
    }

    public static function create(array $data): int {
        return self::submit($data);
    }

    public static function submit(array $data): int {
        return Database::transaction(function () use ($data) {
            $paymentCode = 'PAY-' . strtoupper(bin2hex(random_bytes(4)));

            $paymentId = Database::insert('payments', [
                'payment_code' => $paymentCode,
                'booking_id' => (int)$data['booking_id'],
                'customer_id' => (int)$data['customer_id'],
                'payment_method' => strtolower(trim($data['payment_method'])),
                'sender_number' => trim($data['sender_number']),
                'transaction_id' => strtoupper(trim($data['transaction_id'])),
                'proof_image' => $data['proof_image'] ?? null,
                'amount' => (float)$data['amount'],
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Update booking status
            Booking::updateStatus((int)$data['booking_id'], 'payment_submitted');

            return $paymentId;
        });
    }

    public static function approve(int $paymentId, int $reviewerId): bool {
        return Database::transaction(function () use ($paymentId, $reviewerId) {
            $payment = self::find($paymentId);
            if (!$payment || $payment['status'] !== 'pending') {
                return false;
            }

            // 1. Mark payment as approved
            Database::update('payments', [
                'status' => 'approved',
                'reviewed_by' => $reviewerId,
                'reviewed_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $paymentId]);

            // 2. Mark booking as confirmed
            $bookingId = (int)$payment['booking_id'];
            Booking::updateStatus($bookingId, 'confirmed');

            // 3. Issue digital tickets
            Ticket::issueForBooking($bookingId);

            // 4. Log audit trail
            AuditLog::record(
                $reviewerId,
                'payment.approved',
                'payment',
                $paymentId,
                ['status' => 'pending'],
                ['status' => 'approved', 'booking_id' => $bookingId]
            );

            return true;
        });
    }

    public static function reject(int $paymentId, int $reviewerId, string $reason): bool {
        return Database::transaction(function () use ($paymentId, $reviewerId, $reason) {
            $payment = self::find($paymentId);
            if (!$payment || $payment['status'] !== 'pending') {
                return false;
            }

            // 1. Mark payment as rejected
            Database::update('payments', [
                'status' => 'rejected',
                'rejection_reason' => trim($reason),
                'reviewed_by' => $reviewerId,
                'reviewed_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $paymentId]);

            // 2. Mark booking as cancelled
            $bookingId = (int)$payment['booking_id'];
            Booking::updateStatus($bookingId, 'cancelled');

            // 3. Restore inventory stock for each booking item
            $items = Booking::getItems($bookingId);
            foreach ($items as $item) {
                TicketType::restoreStock((int)$item['ticket_type_id'], (int)$item['quantity']);
            }

            // 4. Log audit trail
            AuditLog::record(
                $reviewerId,
                'payment.rejected',
                'payment',
                $paymentId,
                ['status' => 'pending'],
                ['status' => 'rejected', 'reason' => $reason, 'booking_id' => $bookingId]
            );

            return true;
        });
    }
}
