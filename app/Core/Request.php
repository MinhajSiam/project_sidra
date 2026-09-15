<?php

declare(strict_types=1);

namespace App\Core;

class Request {
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private ?array $json = null;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $server = null,
        ?array $files = null
    ) {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->server = $server ?? $_SERVER;
        $this->files = $files ?? $_FILES;

        // Parse JSON body if applicable
        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->json = $decoded;
            }
        }
    }

    public function method(): string {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($this->post['_method'])) {
            $override = strtoupper($this->post['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                return $override;
            }
        }
        return $method;
    }

    public function isPost(): bool {
        return $this->method() === 'POST';
    }

    public function isGet(): bool {
        return $this->method() === 'GET';
    }

    public function isAjax(): bool {
        return ($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest' || $this->wantsJson();
    }

    public function wantsJson(): bool {
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }

    public function path(): string {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);

        // Normalize base path for subfolder setups
        if ($baseDir !== '/' && $baseDir !== '\\' && str_starts_with($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
        }

        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    public function get(string $key, mixed $default = null): mixed {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed {
        return $this->post[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed {
        $merged = array_merge($this->get, $this->post, $this->json ?? []);
        if ($key === null) {
            return $merged;
        }
        return $merged[$key] ?? $default;
    }

    public function all(): array {
        return $this->input();
    }

    public function file(string $key): ?array {
        $file = $this->files[$key] ?? null;
        if (!$file || !isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return null;
        }
        return $file;
    }

    public function ip(): string {
        if (!empty($this->server['HTTP_CF_CONNECTING_IP'])) {
            return $this->server['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string {
        return substr($this->server['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
    }

    public function header(string $key, ?string $default = null): ?string {
        $normalizedKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$normalizedKey] ?? $this->server[$key] ?? $default;
    }

    public function server(string $key, mixed $default = null): mixed {
        return $this->server[$key] ?? $default;
    }

    public function saveUploadedFile(
        string $fileKey,
        string $destinationDirectory,
        array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'],
        int $maxSizeBytes = 5242880 // 5MB
    ): ?string {
        $file = $this->file($fileKey);
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $maxSizeBytes) {
            throw new \RuntimeException("File exceeds maximum allowed size of " . ($maxSizeBytes / 1024 / 1024) . "MB.");
        }

        // Verify MIME type using fileinfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException("Invalid file format ({$mime}). Allowed formats: " . implode(', ', $allowedMimes));
        }

        // Determine extension safely
        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        $ext = $extMap[$mime] ?? 'bin';

        // Cryptographically secure random filename
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        if (!is_dir($destinationDirectory)) {
            mkdir($destinationDirectory, 0777, true);
        }

        $targetPath = rtrim($destinationDirectory, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException("Failed to store uploaded file.");
        }

        return $filename;
    }
}
