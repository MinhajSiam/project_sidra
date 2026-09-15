<?php

declare(strict_types=1);

namespace App\Core;

class Csrf {
    public static function token(): string {
        Session::start();
        $token = Session::get('_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }
        return $token;
    }

    public static function validate(?string $submittedToken): bool {
        if (!$submittedToken) {
            return false;
        }
        $sessionToken = self::token();
        return hash_equals($sessionToken, $submittedToken);
    }

    public static function regenerate(): string {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set('_csrf_token', $token);
        return $token;
    }
}
