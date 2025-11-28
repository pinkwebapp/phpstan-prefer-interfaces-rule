<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesNullableDateTimeImmutable
{
    private $dateTime;

    public function __construct(?\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }
}
