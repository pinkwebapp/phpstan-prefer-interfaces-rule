<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesUnionFizOrInterface
{
    private $fiz;

    public function __construct(Fiz|FizInterface $fiz)
    {
        $this->fiz = $fiz;
    }
}
