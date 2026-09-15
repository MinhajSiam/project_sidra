<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\Customer;

class AuthController {
    // ------------------------------------------------------------
    // Customer Authentication
    // ------------------------------------------------------------

    public function showLogin(Request $request): Response {
        if (Auth::customerCheck()) {
            redirect('/customer/dashboard');
        }
        return (new Response())->setContent(
            View::render('auth.login', [
                'redirect' => $request->get('redirect', '/customer/dashboard'),
                'layout' => 'layouts.auth',
            ])
        );
    }

    public function login(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/login');
        }

        $login = trim($request->input('login'));
        $password = $request->input('password');

        if (Auth::attemptCustomer($login, $password)) {
            flash('success', 'Welcome back! You are now logged in.');
            $destination = $request->input('redirect', '/customer/dashboard');
            redirect($destination);
        }

        flash('error', 'Invalid login credentials. Please check your email/phone and password.');
        redirect('/login');
    }

    public function showRegister(Request $request): Response {
        if (Auth::customerCheck()) {
            redirect('/customer/dashboard');
        }
        return (new Response())->setContent(
            View::render('auth.register', ['layout' => 'layouts.auth'])
        );
    }

    public function register(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|phone|unique:customers,phone',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/register');
        }

        try {
            Customer::create([
                'name' => trim($request->input('name')),
                'email' => strtolower(trim($request->input('email'))),
                'phone' => trim($request->input('phone')),
                'password' => $request->input('password'),
            ]);

            Auth::attemptCustomer($request->input('email'), $request->input('password'));
            flash('success', 'Account created successfully! Welcome to Sidra.');
            redirect('/customer/dashboard');
        } catch (\Throwable $e) {
            flash('error', 'Registration failed: ' . $e->getMessage());
            redirect('/register');
        }
    }

    public function logout(Request $request): Response {
        Auth::customerLogout();
        flash('success', 'You have been safely logged out.');
        redirect('/');
    }

    public function showForgotPassword(Request $request): Response {
        return (new Response())->setContent(
            View::render('auth.forgot_password', ['layout' => 'layouts.auth'])
        );
    }

    public function sendResetLink(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/forgot-password');
        }

        $email = strtolower(trim($request->input('email')));
        $customer = Customer::findByEmail($email);

        if ($customer) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            Database::insert('password_resets', [
                'email' => $email,
                'token' => $token,
                'expires_at' => $expires,
            ]);

            // In production, send email. For demo/local, flash the reset link!
            $resetUrl = url("reset-password?token={$token}&email=" . urlencode($email));
            flash('success', "Password reset link generated: <a href='{$resetUrl}' class='underline font-bold'>Click here to reset your password</a>");
        } else {
            flash('info', 'If that email address exists in our system, a password reset link has been dispatched.');
        }

        redirect('/forgot-password');
    }

    public function showResetPassword(Request $request): Response {
        $token = $request->get('token');
        $email = $request->get('email');

        if (!$token || !$email) {
            flash('error', 'Invalid password reset request.');
            redirect('/login');
        }

        return (new Response())->setContent(
            View::render('auth.reset_password', [
                'token' => $token,
                'email' => $email,
                'layout' => 'layouts.auth',
            ])
        );
    }

    public function resetPassword(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect($_SERVER['HTTP_REFERER'] ?? '/login');
        }

        $token = $request->input('token');
        $email = strtolower(trim($request->input('email')));

        $now = date('Y-m-d H:i:s');
        $record = Database::fetch(
            "SELECT * FROM `password_resets` WHERE email = :email AND token = :token AND expires_at >= :now LIMIT 1",
            ['email' => $email, 'token' => $token, 'now' => $now]
        );

        if (!$record) {
            flash('error', 'Invalid or expired password reset token.');
            redirect('/forgot-password');
        }

        $customer = Customer::findByEmail($email);
        if ($customer) {
            Customer::updateProfile((int)$customer['id'], [
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'password' => $request->input('password'),
            ]);

            Database::delete('password_resets', 'email = :email', ['email' => $email]);
            flash('success', 'Your password has been successfully reset! Please login.');
            redirect('/login');
        }

        redirect('/login');
    }

    // ------------------------------------------------------------
    // Administrative / Staff Portal Authentication
    // ------------------------------------------------------------

    public function showAdminLogin(Request $request): Response {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user['role_name'] === 'gate_staff') {
                redirect('/gate/scan');
            }
            redirect('/admin');
        }
        return (new Response())->setContent(
            View::render('auth.admin_login', ['layout' => 'layouts.auth'])
        );
    }

    public function adminLogin(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/login');
        }

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        if (Auth::attempt($email, $password)) {
            $user = Auth::user();
            flash('success', "Welcome back, {$user['name']}! Authenticated as {$user['role_display']}.");

            // Gate staff redirects straight to scanner
            if ($user['role_name'] === 'gate_staff') {
                redirect('/gate/scan');
            }

            redirect('/admin');
        }

        flash('error', 'Invalid credentials or inactive administrative account.');
        redirect('/admin/login');
    }

    public function adminLogout(Request $request): Response {
        Auth::logout();
        flash('success', 'You have been logged out of the administration console.');
        redirect('/admin/login');
    }
}
