<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class EventCategory {
    public static function all(bool $activeOnly = true): array {
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM events e WHERE e.category_id = c.id AND e.status = 'published') as event_count 
                FROM `event_categories` c";
        if ($activeOnly) {
            $sql .= " WHERE c.is_active = 1";
        }
        $sql .= " ORDER BY c.name ASC";
        return Database::fetchAll($sql);
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT * FROM `event_categories` WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public static function findBySlug(string $slug): ?array {
        return Database::fetch("SELECT * FROM `event_categories` WHERE slug = :slug LIMIT 1", ['slug' => $slug]);
    }

    public static function create(array $data): int {
        return Database::insert('event_categories', [
            'name' => trim($data['name']),
            'slug' => str_slug($data['name'], 'category'),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'calendar',
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function update(int $id, array $data): int {
        return Database::update('event_categories', [
            'name' => trim($data['name']),
            'slug' => str_slug($data['name'], 'category'),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'calendar',
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ], 'id = :id', ['id' => $id]);
    }
}
