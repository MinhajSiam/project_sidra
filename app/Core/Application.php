<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class Application {
    private static ?Application $instance = null;
    private Router $router;
    private Request $request;

    public function __construct() {
        self::$instance = $this;
        $this->loadEnvironment();
        $this->configureErrorHandling();
        Session::start();
        $this->router = new Router();
        $this->request = new Request();

        // Register Global Middlewares
        $this->router->addGlobalMiddleware(\App\Middleware\SecurityHeadersMiddleware::class);
        $this->router->addGlobalMiddleware(\App\Middleware\CsrfMiddleware::class);
    }

    public static function getInstance(): Application {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getRouter(): Router {
        return $this->router;
    }

    public function run(): void {
        try {
            $response = $this->router->dispatch($this->request);
            Session::ageFlashData();
            $response->send();
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    private function loadEnvironment(): void {
        $envPath = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    $_ENV[$k] = $v;
                    putenv("{$k}={$v}");
                }
            }
        }
    }

    private function configureErrorHandling(): void {
        $debug = config('app.debug', false);
        if ($debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        date_default_timezone_set(config('app.locale.timezone', 'Asia/Dhaka'));
    }

    private function handleException(Throwable $e): void {
        $debug = config('app.debug', false);

        // Log error
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/app.log';
        $entry = sprintf(
            "[%s] %s in %s:%d\nStack Trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        file_put_contents($logFile, $entry, FILE_APPEND);

        if ($this->request->wantsJson()) {
            (new Response())->json([
                'success' => false,
                'message' => $debug ? $e->getMessage() : 'An unexpected server error occurred.',
                'trace' => $debug ? explode("\n", $e->getTraceAsString()) : null,
            ], 500)->send();
            return;
        }

        (new Response())
            ->setStatusCode(500)
            ->setContent(View::render('errors.500', [
                'exception' => $debug ? $e : null,
                'layout' => 'layouts.main'
            ]))
            ->send();
    }
}
