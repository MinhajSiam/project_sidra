<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class CustomerAuthMiddleware {
    public function handle(Request $request, callable $next): Response {
        if (!Auth::customerCheck()) {
            if ($request->wantsJson()) {
                return (new Response())->json([
                    'success' => false,
                    'message' => 'Please login to continue.',
                ], 401);
            }
            flash('error', 'Please login to view your account, bookings, and digital tickets.');
            redirect('/login?redirect=' . urlencode($request->path()));
        }
        return $next($request);
    }
}
