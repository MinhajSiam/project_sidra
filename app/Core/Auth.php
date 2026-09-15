<?php

declare(strict_types=1);

namespace App\Core;

class Auth {
    // ------------------------------------------------------------
    // Administrative / Staff Authentication
    // ------------------------------------------------------------

    public static function attempt(string $email, string $password): bool {
        $user = Database::fetch(
            "SELECT u.*, r.name as role_name, r.display_name as role_display 
             FROM `users` u 
             JOIN `roles` r ON u.role_id = r.id 
             WHERE u.email = :email AND u.status = 'active' LIMIT 1",
            ['email' => strtolower(trim($email))]
        );

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        // Fetch permissions for this role
        $permissions = Database::fetchAll(
            "SELECT p.name 
             FROM `permissions` p 
             JOIN `role_permissions` rp ON p.id = rp.permission_id 
             WHERE rp.role_id = :role_id",
            ['role_id' => $user['role_id']]
        );
        $user['permissions'] = array_column($permissions, 'name');

        Session::regenerate();
        Session::set('auth_user_id', (int)$user['id']);
        Session::set('auth_user', $user);

        // Update last login
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);

        return true;
    }

    public static function user(): ?array {
        Session::start();
        $user = Session::get('auth_user');
        if (!$user && Session::has('auth_user_id')) {
            $userId = Session::get('auth_user_id');
            $user = Database::fetch(
                "SELECT u.*, r.name as role_name, r.display_name as role_display 
                 FROM `users` u 
                 JOIN `roles` r ON u.role_id = r.id 
                 WHERE u.id = :id AND u.status = 'active' LIMIT 1",
                ['id' => $userId]
            );
            if ($user) {
                $permissions = Database::fetchAll(
                    "SELECT p.name 
                     FROM `permissions` p 
                     JOIN `role_permissions` rp ON p.id = rp.permission_id 
                     WHERE rp.role_id = :role_id",
                    ['role_id' => $user['role_id']]
                );
                $user['permissions'] = array_column($permissions, 'name');
                Session::set('auth_user', $user);
            }
        }
        return $user;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function check(): bool {
        return self::user() !== null;
    }

    public static function hasRole(string|array $roles): bool {
        $user = self::user();
        if (!$user) {
            return false;
        }

        $roleName = $user['role_name'] ?? '';
        if ($roleName === 'super_admin') {
            return true; // Super Admin has ultimate clearance
        }

        if (is_array($roles)) {
            return in_array($roleName, $roles, true);
        }

        return $roleName === $roles;
    }

    public static function hasPermission(string $permission): bool {
        $user = self::user();
        if (!$user) {
            return false;
        }

        if (($user['role_name'] ?? '') === 'super_admin') {
            return true;
        }

        $permissions = $user['permissions'] ?? [];
        return in_array($permission, $permissions, true);
    }

    public static function logout(): void {
        Session::remove('auth_user_id');
        Session::remove('auth_user');
        Session::regenerate();
    }

    // ------------------------------------------------------------
    // Public Customer Authentication
    // ------------------------------------------------------------

    public static function attemptCustomer(string $emailOrPhone, string $password): bool {
        $customer = Database::fetch(
            "SELECT * FROM `customers` 
             WHERE (email = :login OR phone = :login) AND status = 'active' LIMIT 1",
            ['login' => trim($emailOrPhone)]
        );

        if (!$customer) {
            return false;
        }

        if (!password_verify($password, $customer['password_hash'])) {
            return false;
        }

        Session::regenerate();
        Session::set('customer_id', (int)$customer['id']);
        Session::set('customer_user', $customer);

        return true;
    }

    public static function customer(): ?array {
        Session::start();
        $customer = Session::get('customer_user');
        if (!$customer && Session::has('customer_id')) {
            $custId = Session::get('customer_id');
            $customer = Database::fetch(
                "SELECT * FROM `customers` WHERE id = :id AND status = 'active' LIMIT 1",
                ['id' => $custId]
            );
            if ($customer) {
                Session::set('customer_user', $customer);
            }
        }
        return $customer;
    }

    public static function customerId(): ?int {
        $cust = self::customer();
        return $cust ? (int)$cust['id'] : null;
    }

    public static function customerCheck(): bool {
        return self::customer() !== null;
    }

    public static function customerLogout(): void {
        Session::remove('customer_id');
        Session::remove('customer_user');
        Session::regenerate();
    }
}
