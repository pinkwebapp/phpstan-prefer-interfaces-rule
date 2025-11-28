<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesBar
{
    private $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
