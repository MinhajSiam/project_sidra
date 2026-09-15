<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Setting {
    public static function all(): array {
        $rows = Database::fetchAll("SELECT * FROM `system_settings` ORDER BY setting_group ASC, id ASC");
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['setting_group']][] = $row;
        }
        return $grouped;
    }

    public static function get(string $key, ?string $default = null): ?string {
        $row = Database::fetch("SELECT setting_value FROM `system_settings` WHERE setting_key = :key LIMIT 1", [
            'key' => $key,
        ]);
        return $row ? $row['setting_value'] : $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general', ?string $description = null): void {
        $existing = Database::fetch("SELECT id FROM `system_settings` WHERE setting_key = :key LIMIT 1", ['key' => $key]);
        if ($existing) {
            Database::update('system_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'setting_key = :key', ['key' => $key]);
        } else {
            Database::insert('system_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
                'description' => $description,
            ]);
        }
    }
}
