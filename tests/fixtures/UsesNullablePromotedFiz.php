<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesNullablePromotedFiz
{
    private $dummy;

    public function __construct(private ?Fiz $fiz)
    {
    }
}
