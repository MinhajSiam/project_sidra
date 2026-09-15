<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware {
    public function handle(Request $request, callable $next): Response {
        if ($request->isPost()) {
            $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
            if (!Csrf::validate($token)) {
                if ($request->wantsJson()) {
                    return (new Response())->json([
                        'success' => false,
                        'message' => 'CSRF verification failed. Please refresh the page and try again.',
                    ], 419);
                }
                flash('error', 'Session expired or invalid form submission. Please try again.');
                redirect($request->path());
            }
        }
        return $next($request);
    }
}
