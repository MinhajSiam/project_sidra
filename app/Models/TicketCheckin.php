<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TicketCheckin {
    public static function all(array $filters = []): array {
        $sql = "SELECT tc.*, t.ticket_code, t.attendee_name, tt.name as ticket_type_name,
                       e.title as event_title, u.name as staff_name
                FROM `ticket_checkins` tc
                JOIN `tickets` t ON tc.ticket_id = t.id
                JOIN `ticket_types` tt ON t.ticket_type_id = tt.id
                JOIN `bookings` b ON t.booking_id = b.id
                JOIN `events` e ON b.event_id = e.id
                LEFT JOIN `users` u ON tc.scanned_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['event_id'])) {
            $sql .= " AND e.id = :event_id";
            $params['event_id'] = (int)$filters['event_id'];
        }

        if (!empty($filters['result'])) {
            $sql .= " AND tc.result = :result";
            $params['result'] = $filters['result'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (t.ticket_code LIKE :search OR t.attendee_name LIKE :search OR tc.gate_name LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY tc.id DESC";

        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)($filters['offset'] ?? 0);
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return Database::fetchAll($sql, $params);
    }

    public static function statsByEvent(int $eventId): array {
        $totalIssued = Database::fetch(
            "SELECT COUNT(*) as total 
             FROM `tickets` t 
             JOIN `bookings` b ON t.booking_id = b.id 
             WHERE b.event_id = :event_id AND t.status IN ('valid', 'used')",
            ['event_id' => $eventId]
        );

        $checkedIn = Database::fetch(
            "SELECT COUNT(*) as total 
             FROM `tickets` t 
             JOIN `bookings` b ON t.booking_id = b.id 
             WHERE b.event_id = :event_id AND t.status = 'used'",
            ['event_id' => $eventId]
        );

        $duplicates = Database::fetch(
            "SELECT COUNT(*) as total 
             FROM `ticket_checkins` tc 
             JOIN `tickets` t ON tc.ticket_id = t.id 
             JOIN `bookings` b ON t.booking_id = b.id 
             WHERE b.event_id = :event_id AND tc.result = 'duplicate'",
            ['event_id' => $eventId]
        );

        $issuedCount = (int)($totalIssued['total'] ?? 0);
        $scannedCount = (int)($checkedIn['total'] ?? 0);
        $duplicateCount = (int)($duplicates['total'] ?? 0);
        $attendanceRate = $issuedCount > 0 ? round(($scannedCount / $issuedCount) * 100, 1) : 0;

        return [
            'total_issued' => $issuedCount,
            'total_checked_in' => $scannedCount,
            'duplicate_attempts' => $duplicateCount,
            'remaining_at_gate' => max(0, $issuedCount - $scannedCount),
            'attendance_rate' => $attendanceRate,
        ];
    }
}
