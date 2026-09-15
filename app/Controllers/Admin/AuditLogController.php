<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\AuditLog;

class AuditLogController {
    public function index(Request $request): Response {
        $logs = AuditLog::all(100);

        return (new Response())->setContent(
            View::render('admin.audit_logs.index', [
                'logs' => $logs,
                'layout' => 'layouts.admin',
            ])
        );
    }
}
