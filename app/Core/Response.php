<?php

declare(strict_types=1);

namespace App\Core;

class Response {
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function setStatusCode(int $code): self {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function header(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function json(array $data, int $statusCode = 200): self {
        $this->setStatusCode($statusCode);
        $this->header('Content-Type', 'application/json; charset=UTF-8');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $this;
    }

    public function redirect(string $url, int $statusCode = 302): self {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = url($url);
        }
        $this->setStatusCode($statusCode);
        $this->header('Location', $url);
        return $this;
    }

    public function send(): void {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }
        echo $this->content;
    }
}
