<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesVariadicFiz
{
    private $dummy;

    public function __construct(Fiz ...$fizzes)
    {
    }
}
