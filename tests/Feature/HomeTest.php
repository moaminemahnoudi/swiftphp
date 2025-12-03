<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use SwiftPHP\Core\Application;

class HomeTest extends TestCase
{
    public function test_home_page_is_accessible()
    {
        // In a real feature test, we would simulate an HTTP request.
        // For now, we just assert that the application can be instantiated without errors.
        $app = new Application();
        $this->assertInstanceOf(Application::class, $app);
    }
}
