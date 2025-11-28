<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesInternalTypes
{
    private $data;

    public function __construct(int $myInt, bool $myBool, string $myString, array $myArray)
    {
        $this->data = [$myInt, $myBool, $myString, $myArray];
    }
}
