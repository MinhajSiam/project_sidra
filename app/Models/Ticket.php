<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Services\QrCodeService;

class Ticket {
    public static function all(array $filters = []): array {
        $sql = "SELECT t.*, tt.name as ticket_type_name, e.title as event_title, e.event_date,
                       v.name as venue_name, c.name as customer_name, c.phone as customer_phone
                FROM `tickets` t
                JOIN `ticket_types` tt ON t.ticket_type_id = tt.id
                JOIN `bookings` b ON t.booking_id = b.id
                JOIN `events` e ON b.event_id = e.id
                JOIN `venues` v ON e.venue_id = v.id
                JOIN `customers` c ON t.customer_id = c.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['event_id'])) {
            $sql .= " AND e.id = :event_id";
            $params['event_id'] = (int)$filters['event_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (t.ticket_code LIKE :search OR t.attendee_name LIKE :search OR t.attendee_phone LIKE :search OR c.name LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY t.id DESC";

        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)($filters['offset'] ?? 0);
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array {
        return self::fetchDetails("WHERE t.id = :id LIMIT 1", ['id' => $id]);
    }

    public static function findByCode(string $code): ?array {
        return self::fetchDetails("WHERE t.ticket_code = :code LIMIT 1", ['code' => strtoupper(trim($code))]);
    }

    public static function findByToken(string $token): ?array {
        return self::fetchDetails("WHERE t.verification_token = :token LIMIT 1", ['token' => trim($token)]);
    }

    public static function getByBookingId(int $bookingId): array {
        $sql = "SELECT t.*, tt.name as ticket_type_name, tt.price as ticket_price
                FROM `tickets` t
                JOIN `ticket_types` tt ON t.ticket_type_id = tt.id
                WHERE t.booking_id = :booking_id
                ORDER BY t.id ASC";
        return Database::fetchAll($sql, ['booking_id' => $bookingId]);
    }

    public static function getByCustomerId(int $customerId): array {
        $sql = "SELECT t.*, tt.name as ticket_type_name, 
                       e.id as event_id, e.title as event_title, e.slug as event_slug, e.event_date, e.start_time, e.end_time, e.banner_image as event_banner,
                       v.name as venue_name, v.address as venue_address, v.city as venue_city
                FROM `tickets` t
                JOIN `ticket_types` tt ON t.ticket_type_id = tt.id
                JOIN `bookings` b ON t.booking_id = b.id
                JOIN `events` e ON b.event_id = e.id
                JOIN `venues` v ON e.venue_id = v.id
                WHERE t.customer_id = :customer_id
                ORDER BY e.event_date DESC, t.id DESC";
        return Database::fetchAll($sql, ['customer_id' => $customerId]);
    }

    public static function issueForBooking(int $bookingId): array {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return [];
        }

        $issuedTickets = [];
        $secretKey = config('app.ticket_secret_key', 'sidra_default_ticket_verification_secret_key');

        foreach ($booking['items'] as $item) {
            $qty = (int)$item['quantity'];
            $ticketTypeId = (int)$item['ticket_type_id'];
            $unitPrice = (float)$item['unit_price'];

            for ($i = 0; $i < $qty; $i++) {
                $ticketCode = self::generateTicketCode();
                $tokenPayload = $ticketCode . '|' . $booking['id'] . '|' . bin2hex(random_bytes(16));
                $verificationToken = hash_hmac('sha256', $tokenPayload, $secretKey);

                // Generate QR Code data (Verification URL)
                $verifyUrl = url("verify/{$verificationToken}");
                $qrCodeSvg = QrCodeService::generateSvg($verifyUrl);

                $ticketId = Database::insert('tickets', [
                    'ticket_code' => $ticketCode,
                    'verification_token' => $verificationToken,
                    'booking_id' => $bookingId,
                    'ticket_type_id' => $ticketTypeId,
                    'customer_id' => (int)$booking['customer_id'],
                    'attendee_name' => $booking['customer_name'],
                    'attendee_email' => $booking['customer_email'],
                    'attendee_phone' => $booking['customer_phone'],
                    'price' => $unitPrice,
                    'status' => 'valid',
                    'qr_code_path' => null, // SVG rendered dynamically or cached
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $issuedTickets[] = $ticketId;
            }
        }

        return $issuedTickets;
    }

    /**
     * Atomic Gate Check-in: Prevents race conditions and duplicate scans.
     */
    public static function processCheckin(string $identifier, int $staffUserId, string $gateName = 'Main Gate'): array {
        return Database::transaction(function () use ($identifier, $staffUserId, $gateName) {
            $identifier = trim($identifier);

            // Lookup ticket by token or ticket code
            $ticket = self::findByToken($identifier) ?? self::findByCode($identifier);

            if (!$ticket) {
                return [
                    'success' => false,
                    'status' => 'invalid',
                    'title' => 'INVALID TICKET',
                    'message' => 'No matching ticket found in database. Fraudulent or unrecognized ticket code.',
                    'ticket' => null,
                ];
            }

            if ($ticket['status'] === 'cancelled') {
                return [
                    'success' => false,
                    'status' => 'cancelled',
                    'title' => 'CANCELLED TICKET',
                    'message' => 'This ticket has been cancelled or refunded.',
                    'ticket' => $ticket,
                ];
            }

            if ($ticket['status'] === 'used') {
                // Fetch previous checkin history
                $lastCheckin = Database::fetch(
                    "SELECT tc.*, u.name as staff_name 
                     FROM `ticket_checkins` tc 
                     LEFT JOIN `users` u ON tc.scanned_by = u.id 
                     WHERE tc.ticket_id = :ticket_id AND tc.result = 'valid' 
                     ORDER BY tc.id DESC LIMIT 1",
                    ['ticket_id' => $ticket['id']]
                );

                // Record duplicate attempt
                Database::insert('ticket_checkins', [
                    'ticket_id' => $ticket['id'],
                    'scanned_by' => $staffUserId,
                    'gate_name' => $gateName,
                    'result' => 'duplicate',
                    'notes' => 'Attempted duplicate entry scan',
                ]);

                return [
                    'success' => false,
                    'status' => 'duplicate',
                    'title' => 'ALREADY USED',
                    'message' => sprintf(
                        "Ticket was already checked in on %s at gate '%s'%s.",
                        $lastCheckin ? format_datetime($lastCheckin['scanned_at']) : 'earlier',
                        $lastCheckin['gate_name'] ?? $gateName,
                        $lastCheckin ? " by {$lastCheckin['staff_name']}" : ""
                    ),
                    'ticket' => $ticket,
                    'first_checkin' => $lastCheckin,
                ];
            }

            // Attempt atomic check-in update
            $updated = Database::update('tickets', [
                'status' => 'used',
            ], 'id = :id AND status = :expected_status', [
                'id' => $ticket['id'],
                'expected_status' => 'valid',
            ]);

            if ($updated === 0) {
                // Another thread checked it in simultaneously
                return [
                    'success' => false,
                    'status' => 'duplicate',
                    'title' => 'CONCURRENT SCAN DETECTED',
                    'message' => 'This ticket was just scanned and admitted by another gate reader.',
                    'ticket' => $ticket,
                ];
            }

            // Record successful checkin
            Database::insert('ticket_checkins', [
                'ticket_id' => $ticket['id'],
                'scanned_by' => $staffUserId,
                'gate_name' => $gateName,
                'result' => 'valid',
                'notes' => 'Admitted entry',
            ]);

            return [
                'success' => true,
                'status' => 'valid',
                'title' => 'ENTRY PERMITTED',
                'message' => 'Valid ticket verified. Attendee admitted.',
                'ticket' => $ticket,
            ];
        });
    }

    private static function fetchDetails(string $whereClause, array $params = []): ?array {
        $sql = "SELECT t.*, tt.name as ticket_type_name, tt.description as ticket_type_desc,
                       b.booking_reference, b.created_at as booking_date,
                       e.id as event_id, e.title as event_title, e.slug as event_slug, e.event_date, e.start_time, e.end_time, e.banner_image as event_banner,
                       v.name as venue_name, v.address as venue_address, v.city as venue_city, v.map_url as venue_map,
                       c.name as customer_name, c.email as customer_email, c.phone as customer_phone
                FROM `tickets` t
                JOIN `ticket_types` tt ON t.ticket_type_id = tt.id
                JOIN `bookings` b ON t.booking_id = b.id
                JOIN `events` e ON b.event_id = e.id
                JOIN `venues` v ON e.venue_id = v.id
                JOIN `customers` c ON t.customer_id = c.id
                {$whereClause}";

        return Database::fetch($sql, $params);
    }

    private static function generateTicketCode(): string {
        $prefix = 'SDR-TKT-';
        do {
            $code = $prefix . strtoupper(bin2hex(random_bytes(2))) . '-' . strtoupper(bin2hex(random_bytes(2)));
            $exists = Database::fetch("SELECT id FROM `tickets` WHERE ticket_code = :code", ['code' => $code]);
        } while ($exists);
        return $code;
    }
}
