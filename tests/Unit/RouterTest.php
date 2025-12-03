<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SwiftPHP\Core\Application;
use SwiftPHP\Core\Router;

class RouterTest extends TestCase
{
    public function test_router_initialization()
    {
        $app = new Application();
        $this->assertInstanceOf(Router::class, $app->getRouter());
    }

    public function test_router_dispatch_closure()
    {
        $app = new Application();
        $router = $app->getRouter();

        $router->addRoute('GET', '/test', function () {
            return 'hello world';
        });

        $response = $router->dispatch('GET', '/test');
        $this->assertEquals('hello world', $response);
    }
}
