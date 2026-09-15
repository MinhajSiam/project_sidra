<?php

declare(strict_types=1);

namespace App\Core;

class Session {
    private static bool $started = false;

    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $lifetime = config('app.session.lifetime', 7200);
        $secure = config('app.session.secure', false);
        $name = config('app.session.name', 'SIDRA_SESSION');

        if (!headers_sent()) {
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_strict_mode', '1');

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_name($name);
            @session_start();
        } elseif (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        self::$started = true;

        // Initialize flash storage
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        if (!isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash_next'] = [];
        }
        if (!isset($_SESSION['_old_input'])) {
            $_SESSION['_old_input'] = [];
        }
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function all(): array {
        self::start();
        return $_SESSION;
    }

    public static function regenerate(): void {
        self::start();
        session_regenerate_id(true);
    }

    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function flash(string $key, mixed $value): void {
        self::start();
        $_SESSION['_flash_next'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public static function setOldInput(array $input): void {
        self::start();
        $_SESSION['_old_input_next'] = $input;
    }

    public static function getOldInput(string $key, mixed $default = ''): mixed {
        self::start();
        return $_SESSION['_old_input'][$key] ?? $default;
    }

    public static function ageFlashData(): void {
        self::start();
        $_SESSION['_flash'] = $_SESSION['_flash_next'] ?? [];
        $_SESSION['_flash_next'] = [];

        $_SESSION['_old_input'] = $_SESSION['_old_input_next'] ?? [];
        $_SESSION['_old_input_next'] = [];
    }
}
