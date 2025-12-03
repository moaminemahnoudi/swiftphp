<?php

declare(strict_types=1);

namespace SwiftPHP\Middleware;

interface Middleware
{
    public function handle($response): mixed;
}
