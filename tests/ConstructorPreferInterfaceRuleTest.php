<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule\Tests;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PinkWeb\PHPStanPreferInterfacesRule\ConstructorPreferInterfaceRule;
use PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures\BazInterface;
use PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures\Fiz;
use PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures\FizBaz;
use PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures\FizInterface;

/**
 * @extends RuleTestCase<ConstructorPreferInterfaceRule>
 */
final class ConstructorPreferInterfaceRuleTest extends RuleTestCase
{
    public function testItSucceedsWithClassWithoutInterface(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesBar.php'], []);
    }

    public function testItSucceedsWithInterface(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesBazInterface.php'], []);
    }

    public function testItSucceedsWithInternalTypes(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesInternalTypes.php'], []);
    }

    public function testItSucceedsWithConcreteImplementationOfExcludedInterface(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesDateTimeImmutable.php'], []);
    }

    public function testItRaisesErrorWhenUsingConcreteClassWithInterface(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesFiz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$fiz" is of type %s but should be one of: %s', Fiz::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItRaisesErrorWithAllPossibleInterfaceDeclarations(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesFizBaz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$fizBaz" is of type %s but should be one of: %s, %s', FizBaz::class, BazInterface::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItRaisesMultipleErrorsWhenMultipleArgumentsAreConcreteInstances(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesFizAndFizBaz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$fiz" is of type %s but should be one of: %s', Fiz::class, FizInterface::class),
                    12,
                ],
                [
                    \sprintf('Constructor argument #1 "$fizBaz" is of type %s but should be one of: %s, %s', FizBaz::class, BazInterface::class, FizInterface::class),
                    12,
                ],
            ],
        );
    }

    protected function getRule(): Rule
    {
        return new ConstructorPreferInterfaceRule([\DateTimeInterface::class], $this->createReflectionProvider());
    }
}
