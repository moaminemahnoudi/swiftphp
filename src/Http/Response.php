<?php

declare(strict_types=1);

namespace SwiftPHP\Http;

class Response
{
    private string $content;
    private int $statusCode;
    private array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function make(string $content = '', int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, $headers);
    }

    public static function json(array $data, int $statusCode = 200): self
    {
        return new self(
            json_encode($data),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        return new self('', $statusCode, ['Location' => $url]);
    }

    public static function view(string $view, array $data = []): self
    {
        $content = \SwiftPHP\View\View::render($view, $data);
        return new self($content);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->content;
    }

    public function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withStatus(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
