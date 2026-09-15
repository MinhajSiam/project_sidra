<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Event {
    public static function all(array $filters = []): array {
        $sql = "SELECT e.*, c.name as category_name, c.slug as category_slug, 
                       v.name as venue_name, v.city as venue_city,
                       (SELECT MIN(price) FROM ticket_types tt WHERE tt.event_id = e.id AND tt.status = 'active') as min_price,
                       (SELECT SUM(remaining_quantity) FROM ticket_types tt WHERE tt.event_id = e.id AND tt.status = 'active') as total_remaining
                FROM `events` e
                JOIN `event_categories` c ON e.category_id = c.id
                JOIN `venues` v ON e.venue_id = v.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['venue_id'])) {
            $sql .= " AND e.venue_id = :venue_id";
            $params['venue_id'] = (int)$filters['venue_id'];
        }

        if (!empty($filters['is_featured'])) {
            $sql .= " AND e.is_featured = 1";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (e.title LIKE :search OR e.summary LIKE :search OR v.name LIKE :search OR v.city LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.event_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        // Sorting
        $sort = $filters['sort'] ?? 'upcoming';
        switch ($sort) {
            case 'date_desc':
                $sql .= " ORDER BY e.event_date DESC, e.start_time DESC";
                break;
            case 'title_asc':
                $sql .= " ORDER BY e.title ASC";
                break;
            case 'upcoming':
            default:
                $sql .= " ORDER BY e.event_date ASC, e.start_time ASC";
                break;
        }

        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)($filters['offset'] ?? 0);
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return Database::fetchAll($sql, $params);
    }

    public static function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) as total 
                FROM `events` e
                JOIN `event_categories` c ON e.category_id = c.id
                JOIN `venues` v ON e.venue_id = v.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (e.title LIKE :search OR e.summary LIKE :search OR v.name LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $row = Database::fetch($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    public static function find(int $id): ?array {
        $event = Database::fetch(
            "SELECT e.*, c.name as category_name, c.slug as category_slug, 
                    v.name as venue_name, v.address as venue_address, v.city as venue_city, v.map_url, v.capacity as venue_capacity
             FROM `events` e
             JOIN `event_categories` c ON e.category_id = c.id
             JOIN `venues` v ON e.venue_id = v.id
             WHERE e.id = :id LIMIT 1",
            ['id' => $id]
        );

        if ($event) {
            $event['ticket_types'] = TicketType::getByEventId((int)$event['id']);
        }

        return $event;
    }

    public static function findBySlug(string $slug): ?array {
        $event = Database::fetch(
            "SELECT e.*, c.name as category_name, c.slug as category_slug, 
                    v.name as venue_name, v.address as venue_address, v.city as venue_city, v.map_url, v.capacity as venue_capacity
             FROM `events` e
             JOIN `event_categories` c ON e.category_id = c.id
             JOIN `venues` v ON e.venue_id = v.id
             WHERE e.slug = :slug LIMIT 1",
            ['slug' => $slug]
        );

        if ($event) {
            $event['ticket_types'] = TicketType::getByEventId((int)$event['id']);
        }

        return $event;
    }

    public static function create(array $data): int {
        return Database::insert('events', [
            'category_id' => (int)$data['category_id'],
            'venue_id' => (int)$data['venue_id'],
            'created_by' => $data['created_by'] ?? null,
            'title' => trim($data['title']),
            'slug' => self::generateUniqueSlug($data['title']),
            'summary' => trim($data['summary']),
            'description' => trim($data['description']),
            'banner_image' => $data['banner_image'] ?? null,
            'event_date' => $data['event_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => $data['status'] ?? 'draft',
            'is_featured' => isset($data['is_featured']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function update(int $id, array $data): int {
        $updateFields = [
            'category_id' => (int)$data['category_id'],
            'venue_id' => (int)$data['venue_id'],
            'title' => trim($data['title']),
            'summary' => trim($data['summary']),
            'description' => trim($data['description']),
            'event_date' => $data['event_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => $data['status'] ?? 'draft',
            'is_featured' => isset($data['is_featured']) ? 1 : 0,
        ];

        if (!empty($data['banner_image'])) {
            $updateFields['banner_image'] = $data['banner_image'];
        }

        return Database::update('events', $updateFields, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): int {
        return Database::delete('events', 'id = :id', ['id' => $id]);
    }

    private static function generateUniqueSlug(string $title, ?int $ignoreId = null): string {
        $base = str_slug($title, 'event');
        $slug = $base;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM `events` WHERE slug = :slug";
            $params = ['slug' => $slug];
            if ($ignoreId !== null) {
                $sql .= " AND id != :ignore_id";
                $params['ignore_id'] = $ignoreId;
            }
            $exists = Database::fetch($sql, $params);
            if (!$exists) {
                break;
            }
            $slug = "{$base}-" . (++$counter);
        }

        return $slug;
    }
}
