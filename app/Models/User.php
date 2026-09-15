<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User {
    public static function all(): array {
        return Database::fetchAll(
            "SELECT u.*, r.display_name as role_display 
             FROM `users` u 
             JOIN `roles` r ON u.role_id = r.id 
             ORDER BY u.id DESC"
        );
    }

    public static function find(int $id): ?array {
        return Database::fetch(
            "SELECT u.*, r.name as role_name, r.display_name as role_display 
             FROM `users` u 
             JOIN `roles` r ON u.role_id = r.id 
             WHERE u.id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function create(array $data): int {
        return Database::insert('users', [
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function update(int $id, array $data): int {
        $fields = [
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];
        if (!empty($data['password'])) {
            $fields['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        return Database::update('users', $fields, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): int {
        return Database::delete('users', 'id = :id', ['id' => $id]);
    }
}
