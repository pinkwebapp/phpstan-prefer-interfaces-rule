<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesPromotedReadonlyFiz
{
    private $dummy;

    public function __construct(public readonly Fiz $fiz)
    {
    }
}
