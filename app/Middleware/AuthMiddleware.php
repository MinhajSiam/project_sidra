<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class AuthMiddleware {
    public function handle(Request $request, callable $next): Response {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return (new Response())->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please login.',
                ], 401);
            }
            flash('error', 'Please login to access the administration portal.');
            redirect('/admin/login');
        }
        return $next($request);
    }
}
