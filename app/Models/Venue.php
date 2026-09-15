<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Venue {
    public static function all(): array {
        return Database::fetchAll(
            "SELECT v.*, (SELECT COUNT(*) FROM events e WHERE e.venue_id = v.id) as event_count 
             FROM `venues` v 
             ORDER BY v.name ASC"
        );
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT * FROM `venues` WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public static function create(array $data): int {
        return Database::insert('venues', [
            'name' => trim($data['name']),
            'slug' => str_slug($data['name'], 'venue'),
            'address' => trim($data['address']),
            'city' => trim($data['city'] ?? 'Dhaka'),
            'capacity' => (int)($data['capacity'] ?? 1000),
            'map_url' => $data['map_url'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function update(int $id, array $data): int {
        return Database::update('venues', [
            'name' => trim($data['name']),
            'slug' => str_slug($data['name'], 'venue'),
            'address' => trim($data['address']),
            'city' => trim($data['city'] ?? 'Dhaka'),
            'capacity' => (int)($data['capacity'] ?? 1000),
            'map_url' => $data['map_url'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
        ], 'id = :id', ['id' => $id]);
    }
}
