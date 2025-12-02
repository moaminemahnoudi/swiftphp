<?php

namespace SwiftPHP\Middleware;

use SwiftPHP\Auth\{Auth, Tenant};

class TenantMiddleware implements Middleware
{
    public function handle($response): mixed
    {
        if (!Auth::check()) {
            http_response_code(401);
            return json(['error' => 'Unauthorized'], 401);
        }

        $tenantId = Auth::tenantId();
        
        if ($tenantId === null) {
            http_response_code(403);
            return json(['error' => 'No tenant assigned'], 403);
        }

        Tenant::set($tenantId);

        return $response;
    }
}
