<?php

namespace SwiftPHP\Core;

use SwiftPHP\View\View;

abstract class Controller
{
    protected function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }

    protected function json(array $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}