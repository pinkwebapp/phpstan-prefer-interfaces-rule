<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures;

final class UsesDocblockUnionNoType
{
    private $fiz;

    /**
     * @param Fiz|FizInterface $fiz
     */
    public function __construct($fiz)
    {
        $this->fiz = $fiz;
    }
}
