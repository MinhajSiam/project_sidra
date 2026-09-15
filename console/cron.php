<?php

declare(strict_types=1);

/**
 * Sidra Event Ticketing Platform - Background Maintenance Worker
 *
 * Usage:
 *   php console/cron.php
 *
 * Cron Configuration:
 *   Every 5 or 10 minutes:
 *   * /10 * * * * php /path/to/sidra/console/cron.php >> /path/to/sidra/storage/logs/cron.log 2>&1
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Access denied. CLI only.\n";
    exit(1);
}

require_once __DIR__ . '/../app/Core/Helpers.php';
require_once __DIR__ . '/../app/Core/Database.php';

load_env();

use App\Core\Database;

$timestamp = date('Y-m-d H:i:s');
echo "[{$timestamp}] Starting Sidra scheduled maintenance task...\n";

// 1. Release expired pending reservations (unpaid after 30 minutes)
try {
    $cutoff = date('Y-m-d H:i:s', time() - (30 * 60)); // 30 minutes threshold
    $unpaidBookings = Database::fetchAll(
        "SELECT id, booking_reference FROM bookings 
         WHERE status = 'pending_payment' AND created_at < :cutoff",
        [':cutoff' => $cutoff]
    );

    $releasedCount = 0;
    foreach ($unpaidBookings as $b) {
        $bookingId = (int)$b['id'];
        Database::transaction(function () use ($bookingId, &$releasedCount) {
            // Fetch items
            $items = Database::fetchAll(
                "SELECT ticket_type_id, quantity FROM booking_items WHERE booking_id = :bid",
                [':bid' => $bookingId]
            );

            foreach ($items as $item) {
                // Restore stock atomically
                $typeId = (int)$item['ticket_type_id'];
                $qty = (int)$item['quantity'];

                Database::query(
                    "UPDATE ticket_types 
                     SET remaining_quantity = CASE 
                         WHEN remaining_quantity + :qty1 > total_quantity THEN total_quantity 
                         ELSE remaining_quantity + :qty2 
                     END 
                     WHERE id = :id",
                    [
                        ':qty1' => $qty,
                        ':qty2' => $qty,
                        ':id' => $typeId,
                    ]
                );
            }

            // Mark booking cancelled
            Database::update('bookings', [
                'status' => 'cancelled',
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $bookingId]);

            $releasedCount++;
        });
    }

    echo "  - Cancelled {$releasedCount} expired pending booking(s) and restored reserved inventory.\n";
} catch (Throwable $e) {
    echo "  - Error expiring bookings: " . $e->getMessage() . "\n";
}

// 2. Purge expired password reset tokens
try {
    $now = date('Y-m-d H:i:s');
    $purged = Database::query("DELETE FROM password_resets WHERE expires_at < :now", [':now' => $now]);
    echo "  - Purged expired password reset token records.\n";
} catch (Throwable $e) {
    echo "  - Error purging reset tokens: " . $e->getMessage() . "\n";
}

// 3. Check storage/logs size and manage log rotation if > 10MB
try {
    $logFile = dirname(__DIR__) . '/storage/logs/app.log';
    if (file_exists($logFile) && filesize($logFile) > (10 * 1024 * 1024)) {
        $rotatedName = dirname(__DIR__) . '/storage/logs/app-' . date('Ymd-His') . '.log';
        rename($logFile, $rotatedName);
        touch($logFile);
        echo "  - Rotated large log file (>10MB) to " . basename($rotatedName) . ".\n";
    }
} catch (Throwable $e) {
    echo "  - Error checking log file: " . $e->getMessage() . "\n";
}

echo "[{$timestamp}] Scheduled maintenance complete.\n";
