<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SwiftPHP\Core\Container;

class ContainerTest extends TestCase
{
    public function test_bind_and_resolve()
    {
        $container = new Container();
        $container->bind('foo', function () {
            return 'bar';
        });

        $this->assertEquals('bar', $container->resolve('foo'));
    }

    public function test_singleton()
    {
        $container = new Container();
        $container->singleton('random', function () {
            return rand(1, 1000);
        });

        $first = $container->resolve('random');
        $second = $container->resolve('random');

        $this->assertEquals($first, $second);
    }

    public function test_resolve_class()
    {
        $container = new Container();
        $instance = $container->resolve(ContainerTestClass::class);

        $this->assertInstanceOf(ContainerTestClass::class, $instance);
    }
}

class ContainerTestClass
{
}
