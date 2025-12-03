<?php

declare(strict_types=1);

namespace SwiftPHP\Middleware;

class CorsMiddleware implements Middleware
{
    public function handle($response): mixed
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        return $response;
    }
}
