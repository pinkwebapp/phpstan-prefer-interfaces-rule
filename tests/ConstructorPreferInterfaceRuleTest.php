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
use PinkWeb\PHPStanPreferInterfacesRule\Tests\Fixtures\ExtendedFiz;

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

    public function testItSucceedsWithNullableExcludedInterfaceImplementation(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesNullableDateTimeImmutable.php'], []);
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

    public function testItRaisesErrorWhenUsingNullableConcreteClass(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesNullableFiz.php'],
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

    public function testItIgnoresUnionTypes(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesUnionFizOrInterface.php'], []);
    }

    public function testItIgnoresIntersectionTypes(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesIntersection.php'], []);
    }

    public function testItIgnoresUntypedParameters(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesNoType.php'], []);
    }

    public function testItRaisesErrorWithPromotedProperty(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesPromotedFiz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$%s" is of type %s but should be one of: %s', 'fiz', Fiz::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItRaisesErrorWithPromotedReadonlyProperty(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesPromotedReadonlyFiz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$%s" is of type %s but should be one of: %s', 'fiz', Fiz::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItRaisesErrorWithNullablePromotedProperty(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesNullablePromotedFiz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$%s" is of type %s but should be one of: %s', 'fiz', Fiz::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItRaisesErrorForVariadicParameter(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesVariadicFiz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$%s" is of type %s but should be one of: %s', 'fizzes', Fiz::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItRaisesErrorWhenConcreteExtendsAbstractAndImplementsInterface(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/UsesExtendedFiz.php'],
            [
                [
                    \sprintf('Constructor argument #0 "$%s" is of type %s but should be one of: %s', 'extendedFiz', ExtendedFiz::class, FizInterface::class),
                    11,
                ],
            ],
        );
    }

    public function testItSkipsWhenDocblockUnionWithoutNativeType(): void
    {
        $this->analyse([__DIR__ . '/fixtures/UsesDocblockUnionNoType.php'], []);
    }

    protected function getRule(): Rule
    {
        return new ConstructorPreferInterfaceRule([\DateTimeInterface::class], $this->createReflectionProvider());
    }
}
