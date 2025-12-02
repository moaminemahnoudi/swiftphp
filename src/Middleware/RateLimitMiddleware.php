<?php

namespace SwiftPHP\Middleware;

class RateLimitMiddleware implements Middleware
{
    private int $maxRequests = 60;
    private int $perSeconds = 60;

    public function handle($response): mixed
    {
        session_start();
        
        $key = $_SERVER['REMOTE_ADDR'];
        $now = time();
        
        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = ['count' => 0, 'reset' => $now + $this->perSeconds];
        }
        
        $rateLimit = $_SESSION['rate_limit'][$key];
        
        if ($now > $rateLimit['reset']) {
            $_SESSION['rate_limit'][$key] = ['count' => 1, 'reset' => $now + $this->perSeconds];
        } else {
            $_SESSION['rate_limit'][$key]['count']++;
            
            if ($rateLimit['count'] > $this->maxRequests) {
                http_response_code(429);
                return json_encode(['error' => 'Too many requests']);
            }
        }
        
        return $response;
    }
}
