<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesFiz
{
    private $fiz;

    public function __construct(Fiz $fiz)
    {
        $this->fiz = $fiz;
    }
}
