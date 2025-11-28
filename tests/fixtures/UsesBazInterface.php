<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesBazInterface
{
    private $baz;

    public function __construct(BazInterface $baz)
    {
        $this->baz = $baz;
    }
}
