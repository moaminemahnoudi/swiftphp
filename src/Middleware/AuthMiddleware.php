<?php

namespace SwiftPHP\Middleware;

use SwiftPHP\Http\Request;

class AuthMiddleware implements Middleware
{
    public function handle($response): mixed
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['error' => 'Unauthorized']);
        }
        
        return $response;
    }
}
