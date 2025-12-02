<?php

namespace SwiftPHP\Middleware;

interface Middleware
{
    public function handle($response): mixed;
}
