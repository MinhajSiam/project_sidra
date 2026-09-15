<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TicketType {
    public static function getByEventId(int $eventId, bool $activeOnly = false): array {
        $sql = "SELECT * FROM `ticket_types` WHERE event_id = :event_id";
        if ($activeOnly) {
            $sql .= " AND status = 'active'";
        }
        $sql .= " ORDER BY price ASC";
        return Database::fetchAll($sql, ['event_id' => $eventId]);
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT * FROM `ticket_types` WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public static function create(array $data): int {
        $totalQty = (int)$data['total_quantity'];
        return Database::insert('ticket_types', [
            'event_id' => (int)$data['event_id'],
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'price' => (float)$data['price'],
            'total_quantity' => $totalQty,
            'remaining_quantity' => $totalQty,
            'max_per_user' => (int)($data['max_per_user'] ?? 5),
            'sales_start' => !empty($data['sales_start']) ? $data['sales_start'] : null,
            'sales_end' => !empty($data['sales_end']) ? $data['sales_end'] : null,
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function update(int $id, array $data): int {
        $existing = self::find($id);
        if (!$existing) {
            return 0;
        }

        $newTotal = (int)$data['total_quantity'];
        $diff = $newTotal - (int)$existing['total_quantity'];
        $newRemaining = max(0, (int)$existing['remaining_quantity'] + $diff);

        return Database::update('ticket_types', [
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'price' => (float)$data['price'],
            'total_quantity' => $newTotal,
            'remaining_quantity' => $newRemaining,
            'max_per_user' => (int)($data['max_per_user'] ?? 5),
            'sales_start' => !empty($data['sales_start']) ? $data['sales_start'] : null,
            'sales_end' => !empty($data['sales_end']) ? $data['sales_end'] : null,
            'status' => $data['status'] ?? 'active',
        ], 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): int {
        return Database::delete('ticket_types', 'id = :id', ['id' => $id]);
    }

    /**
     * Atomically decrements remaining stock. Returns true if successful, false if insufficient inventory.
     */
    public static function decrementStock(int $id, int $quantity): bool {
        $sql = "UPDATE `ticket_types` 
                SET remaining_quantity = remaining_quantity - :qty 
                WHERE id = :id AND remaining_quantity >= :qty AND status = 'active'";
        $stmt = Database::query($sql, ['id' => $id, 'qty' => $quantity]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Restores stock when a booking or payment is rejected/cancelled.
     */
    public static function restoreStock(int $id, int $quantity): void {
        $sql = "UPDATE `ticket_types` 
                SET remaining_quantity = CASE 
                    WHEN remaining_quantity + :qty1 > total_quantity THEN total_quantity 
                    ELSE remaining_quantity + :qty2 
                END 
                WHERE id = :id";
        Database::query($sql, ['id' => $id, 'qty1' => $quantity, 'qty2' => $quantity]);
    }
}
