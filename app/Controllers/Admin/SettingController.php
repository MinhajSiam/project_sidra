<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Setting;

class SettingController {
    public function index(Request $request): Response {
        $settingsGrouped = Setting::all();

        return (new Response())->setContent(
            View::render('admin.settings.index', [
                'groups' => $settingsGrouped,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function update(Request $request): Response {
        $settings = $request->input('settings', []);
        $oldSettings = Setting::all();

        foreach ($settings as $key => $value) {
            Setting::set($key, trim((string)$value));
        }

        AuditLog::record(Auth::id(), 'settings.updated', 'setting', null, null, $settings);
        flash('success', 'Platform settings updated successfully.');
        redirect('/admin/settings');
    }
}
