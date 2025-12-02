<?php

namespace SwiftPHP\Attributes;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Inject
{
    public function __construct(public string $class)
    {
    }
}
