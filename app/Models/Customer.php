<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Customer {
    public static function all(): array {
        return Database::fetchAll(
            "SELECT c.*, 
                (SELECT COUNT(*) FROM bookings b WHERE b.customer_id = c.id) as total_bookings,
                (SELECT COUNT(*) FROM tickets t WHERE t.customer_id = c.id) as total_tickets
             FROM `customers` c 
             ORDER BY c.id DESC"
        );
    }

    public static function find(int $id): ?array {
        return Database::fetch(
            "SELECT * FROM `customers` WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function findByEmail(string $email): ?array {
        return Database::fetch(
            "SELECT * FROM `customers` WHERE email = :email LIMIT 1",
            ['email' => strtolower(trim($email))]
        );
    }

    public static function create(array $data): int {
        return Database::insert('customers', [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function updateProfile(int $id, array $data): int {
        $fields = [
            'name' => trim($data['name']),
            'phone' => trim($data['phone']),
        ];
        if (!empty($data['password'])) {
            $fields['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        return Database::update('customers', $fields, 'id = :id', ['id' => $id]);
    }
}
