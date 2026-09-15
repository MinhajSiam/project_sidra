<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\EventCategory;

class EventCategoryController {
    public function index(Request $request): Response {
        $categories = EventCategory::all(false);
        return (new Response())->setContent(
            View::render('admin.categories.index', [
                'categories' => $categories,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function store(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/categories');
        }

        EventCategory::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon', 'calendar'),
            'is_active' => $request->input('is_active') ? 1 : 0,
        ]);

        AuditLog::record(Auth::id(), 'category.created', 'category', null, null, ['name' => $request->input('name')]);
        flash('success', 'Category created successfully.');
        redirect('/admin/categories');
    }

    public function update(Request $request, string $id): Response {
        $categoryId = (int)$id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/categories');
        }

        EventCategory::update($categoryId, [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon', 'calendar'),
            'is_active' => $request->input('is_active') ? 1 : 0,
        ]);

        AuditLog::record(Auth::id(), 'category.updated', 'category', $categoryId);
        flash('success', 'Category updated successfully.');
        redirect('/admin/categories');
    }
}
