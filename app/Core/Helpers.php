<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('load_env')) {
    function load_env(?string $path = null): void {
        $envPath = $path ?? (dirname(__DIR__, 2) . '/.env');
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    $_ENV[$k] = $v;
                    putenv("{$k}={$v}");
                }
            }
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed {
        static $configs = [];
        $parts = explode('.', $key);
        $file = array_shift($parts);

        if (!isset($configs[$file])) {
            $path = dirname(__DIR__) . "/Config/{$file}.php";
            if (file_exists($path)) {
                $configs[$file] = require $path;
            } else {
                $configs[$file] = [];
            }
        }

        $value = $configs[$file];
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $base = rtrim(config('app.url', 'http://localhost:8000'), '/');
        $path = ltrim($path, '/');
        return $path ? "{$base}/{$path}" : $base;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    function upload_url(?string $path): string {
        if (!$path) {
            return asset('images/placeholder.jpg');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return url(ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302): void {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = url($url);
        }
        header("Location: {$url}", true, $status);
        exit;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed {
        if ($key === null) {
            return Session::all();
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value = null): mixed {
        if ($value !== null) {
            Session::flash($key, $value);
            return null;
        }
        return Session::getFlash($key);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed {
        return Session::getOldInput($key, $default);
    }
}

if (!function_exists('auth')) {
    function auth(): ?array {
        return Auth::user();
    }
}

if (!function_exists('customer')) {
    function customer(): ?array {
        return Auth::customer();
    }
}

if (!function_exists('format_currency')) {
    function format_currency(float|int|string $amount): string {
        $symbol = config('app.locale.currency_symbol', '৳');
        return $symbol . ' ' . number_format((float)$amount, 2);
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime(string $datetime, string $format = 'd M Y, h:i A'): string {
        try {
            $dt = new DateTime($datetime);
            return $dt->format($format);
        } catch (Throwable) {
            return $datetime;
        }
    }
}

if (!function_exists('format_date')) {
    function format_date(string $date, string $format = 'd M, Y'): string {
        try {
            $dt = new DateTime($date);
            return $dt->format($format);
        } catch (Throwable) {
            return $date;
        }
    }
}

if (!function_exists('format_time')) {
    function format_time(string $time, string $format = 'h:i A'): string {
        try {
            $dt = new DateTime($time);
            return $dt->format($format);
        } catch (Throwable) {
            return $time;
        }
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $text, string $fallbackPrefix = 'item'): string {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $text), '-'));
        if ($slug === '') {
            $slug = $fallbackPrefix . '-' . bin2hex(random_bytes(3));
        }
        return $slug;
    }
}
