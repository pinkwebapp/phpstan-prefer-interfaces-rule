<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesNoType
{
    private $dep;

    public function __construct($dep)
    {
        $this->dep = $dep;
    }
}
