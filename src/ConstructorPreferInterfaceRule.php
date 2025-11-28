<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * @implements Rule<ClassMethod>
 */
final class ConstructorPreferInterfaceRule implements Rule
{
    /**
     * @var string[]
     */
    private array $excludedInterfaces;

    private ReflectionProvider $reflectionProvider;

    /**
     * @param string[] $excludedInterfaces List of interfaces to ignore
     */
    public function __construct(
        array $excludedInterfaces,
        ReflectionProvider $reflectionProvider,
    ) {
        $this->excludedInterfaces = $excludedInterfaces;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     *
     * @return array<IdentifierRuleError>
     *
     * @throws ShouldNotHappenException
     * @throws MissingMethodFromReflectionException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $methodName = (string) $node->name;
        if ($methodName !== '__construct') {
            return [];
        }
        if (\count($node->getParams()) === 0) {
            return [];
        }
        $class = $scope->getClassReflection();
        if ($class === null) {
            throw new ShouldNotHappenException();
        }
        $errors = [];
        $method = $class->getNativeMethod($methodName);
        $variants = $method->getVariants();
        if (\count($variants) === 0) {
            return [];
        }
        $firstParameters = $variants[0]->getParameters();

        foreach ($firstParameters as $offset => $firstParameter) {
            $agreedClassName = null;
            $allAgree = true;

            foreach ($variants as $variant) {
                $parameters = $variant->getParameters();
                if (!isset($parameters[$offset])) {
                    $allAgree = false;
                    break;
                }

                $normalized = $this->normalizeParameterType($parameters[$offset]->getType());
                if ($normalized === null) {
                    $allAgree = false;
                    break;
                }

                $className = $normalized->getClassName();
                if ($agreedClassName === null) {
                    $agreedClassName = $className;
                } elseif ($agreedClassName !== $className) {
                    $allAgree = false;
                    break;
                }
            }

            if ($allAgree === false || $agreedClassName === null) {
                continue;
            }

            if ($this->reflectionProvider->hasClass($agreedClassName) === false) {
                continue;
            }

            $parameterClass = $this->reflectionProvider->getClass($agreedClassName);

            $availableInterfaces = \array_filter(
                $parameterClass->getInterfaces(),
                function (ClassReflection $interfaceReflection): bool {
                    return $this->isExcluded($interfaceReflection->getName()) === false;
                },
            );

            if ($parameterClass->isInterface() === false && \count($availableInterfaces) > 0) {
                $interfaceNames = \array_map(
                    static fn (ClassReflection $interface): string => $interface->getName(),
                    $availableInterfaces,
                );
                sort($interfaceNames);
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Constructor argument #%d "$%s" is of type %s but should be one of: %s',
                    $offset,
                    $firstParameter->getName(),
                    $agreedClassName,
                    \implode(', ', $interfaceNames),
                ))->identifier('pinkweb.constructor.preferInterface')->build();
            }
        }

        return $errors;
    }

    /**
     * Normalize the parameter type to a single ObjectType if applicable.
     *
     * Rules:
     * - A nullable object (?T) is treated as T.
     * - Multi-type unions are ignored (return null).
     * - Intersections are ignored (return null).
     * - Non-object types are ignored (return null).
     */
    private function normalizeParameterType(Type $type): ?ObjectType
    {
        $type = TypeCombinator::removeNull($type);

        $classNames = $type->getObjectClassNames();
        if (\count($classNames) === 1) {
            return new ObjectType($classNames[0]);
        }

        return null;
    }

    /**
     * Determine whether an interface FQCN is excluded by the configured patterns.
     * Supports exact names and glob-like patterns with '*', e.g. 'Psr\\*'.
     */
    private function isExcluded(string $interfaceName): bool
    {
        foreach ($this->excludedInterfaces as $pattern) {
            if ($this->patternMatches($pattern, $interfaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Glob pattern matcher for FQCNs. Converts a simple pattern with '*' wildcards
     * into a regex and applies it to the given subject.
     */
    private function patternMatches(string $pattern, string $subject): bool
    {
        $escaped = \preg_quote($pattern, '/');
        $regex = '/^' . \str_replace('\\*', '.*', $escaped) . '$/i';

        return \preg_match($regex, $subject) === 1;
    }
}
