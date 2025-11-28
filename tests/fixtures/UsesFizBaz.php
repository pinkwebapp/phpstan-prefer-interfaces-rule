<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesFizBaz
{
    private $fizBaz;

    public function __construct(FizBaz $fizBaz)
    {
        $this->fizBaz = $fizBaz;
    }
}
