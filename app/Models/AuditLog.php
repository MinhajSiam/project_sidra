<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Database;

class AuditLog {
    public static function all(int $limit = 100, int $offset = 0): array {
        return Database::fetchAll(
            "SELECT a.*, u.name as user_name, u.email as user_email, r.display_name as role_name
             FROM `audit_logs` a
             LEFT JOIN `users` u ON a.user_id = u.id
             LEFT JOIN `roles` r ON u.role_id = r.id
             ORDER BY a.id DESC
             LIMIT {$limit} OFFSET {$offset}"
        );
    }

    public static function record(
        ?int $userId,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): int {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI', 0, 255);

        return Database::insert('audit_logs', [
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
