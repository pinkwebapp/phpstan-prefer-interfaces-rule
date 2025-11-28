<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesExtendedFiz
{
    private $dummy;

    public function __construct(ExtendedFiz $extendedFiz)
    {
        $this->dummy = $extendedFiz;
    }
}
