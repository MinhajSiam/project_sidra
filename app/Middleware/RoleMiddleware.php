<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class RoleMiddleware {
    private array $allowedRoles;

    public function __construct(string|array ...$roles) {
        $flat = [];
        foreach ($roles as $r) {
            if (is_array($r)) {
                $flat = array_merge($flat, $r);
            } else {
                $flat[] = $r;
            }
        }
        $this->allowedRoles = $flat;
    }

    public function handle(Request $request, callable $next): Response {
        if (!Auth::check()) {
            redirect('/admin/login');
        }

        if (!Auth::hasRole($this->allowedRoles)) {
            if ($request->wantsJson()) {
                return (new Response())->json([
                    'success' => false,
                    'message' => 'Forbidden: You do not have permission for this module.',
                ], 403);
            }
            return (new Response())
                ->setStatusCode(403)
                ->setContent(\App\Core\View::render('errors.403', ['layout' => 'layouts.main']));
        }

        return $next($request);
    }
}
