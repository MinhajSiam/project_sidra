<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\User;

class UserController {
    public function index(Request $request): Response {
        $users = User::all();
        $roles = Database::fetchAll("SELECT * FROM `roles` ORDER BY id ASC");

        return (new Response())->setContent(
            View::render('admin.users.index', [
                'users' => $users,
                'roles' => $roles,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function store(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|integer',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/users');
        }

        $userId = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'role_id' => (int)$request->input('role_id'),
            'password' => $request->input('password'),
            'status' => 'active',
        ]);

        AuditLog::record(Auth::id(), 'user.created', 'user', $userId, null, ['email' => $request->input('email')]);
        flash('success', 'Staff account created successfully.');
        redirect('/admin/users');
    }

    public function update(Request $request, string $id): Response {
        $userId = (int)$id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'email' => "required|email|unique:users,email,{$userId}",
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/users');
        }

        User::update($userId, [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'role_id' => (int)$request->input('role_id'),
            'status' => $request->input('status', 'active'),
            'password' => $request->input('password') ?: null,
        ]);

        AuditLog::record(Auth::id(), 'user.updated', 'user', $userId);
        flash('success', 'Staff account updated.');
        redirect('/admin/users');
    }
}
