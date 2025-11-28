<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesPromotedFiz
{
    private $dummy;

    public function __construct(private Fiz $fiz)
    {
    }
}
