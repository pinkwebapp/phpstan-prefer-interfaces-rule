<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesFizAndFizBaz
{
    private $fiz;
    private $fizBaz;

    public function __construct(Fiz $fiz, FizBaz $fizBaz)
    {
        $this->fiz = $fiz;
        $this->fizBaz = $fizBaz;
    }
}
