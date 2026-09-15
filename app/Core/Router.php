<?php

declare(strict_types=1);

namespace App\Core;

class Router {
    private array $routes = [];
    private array $globalMiddlewares = [];

    public function addGlobalMiddleware(object|string $middleware): self {
        $this->globalMiddlewares[] = is_string($middleware) ? new $middleware() : $middleware;
        return $this;
    }

    public function get(string $path, array|callable $handler, array $middlewares = []): self {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): self {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): self {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): self {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): self {
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        // Convert {param} into regex (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'regex' => $regex,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];

        return $this;
    }

    public function dispatch(Request $request): Response {
        $method = $request->method();
        $isHead = ($method === 'HEAD');
        $effectiveMethod = $isHead ? 'GET' : $method;
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $effectiveMethod) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                // Extract named route parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }

                // Build complete middleware pipeline: Global Middlewares + Route Middlewares
                $pipeline = array_merge($this->globalMiddlewares, $route['middlewares']);

                $runner = function (Request $req) use ($route, $params) {
                    return $this->invokeHandler($route['handler'], $req, $params);
                };

                // Chain middlewares in reverse
                foreach (array_reverse($pipeline) as $mw) {
                    $middlewareInstance = is_string($mw) ? new $mw() : $mw;
                    $next = $runner;
                    $runner = function (Request $req) use ($middlewareInstance, $next) {
                        return $middlewareInstance->handle($req, $next);
                    };
                }

                $response = $runner($request);
                if ($isHead) {
                    $response->setContent('');
                }
                return $response;
            }
        }

        // 404 Not Found
        if ($request->wantsJson()) {
            return (new Response())->json([
                'success' => false,
                'message' => 'The requested endpoint does not exist.',
            ], 404);
        }

        return (new Response())
            ->setStatusCode(404)
            ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
    }

    private function invokeHandler(array|callable $handler, Request $request, array $params): Response {
        if (is_callable($handler)) {
            $result = call_user_func($handler, $request, $params);
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
            $instance = new $class();
            $result = call_user_func_array([$instance, $method], array_merge([$request], array_values($params)));
        } else {
            throw new \RuntimeException("Invalid route handler format.");
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_string($result)) {
            return (new Response())->setContent($result);
        }

        if (is_array($result)) {
            return (new Response())->json($result);
        }

        return new Response();
    }
}
