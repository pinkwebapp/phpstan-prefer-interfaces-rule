<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesIntersection
{
    private $service;

    public function __construct(BazInterface & FizInterface $service)
    {
        $this->service = $service;
    }
}
