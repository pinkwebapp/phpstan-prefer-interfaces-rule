<?php

declare(strict_types=1);

namespace PinkWeb\PHPStanPreferInterfacesRule;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedParameterReflection;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

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
    public function __construct(array $excludedInterfaces, ReflectionProvider $reflectionProvider)
    {
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
     * @return list<IdentifierRuleError>
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
        $parametersAcceptor = $variants[0];

        /** @var ExtendedParameterReflection $parameter */
        foreach ($parametersAcceptor->getParameters() as $offset => $parameter) {
            $parameterType = $parameter->getType();
            $classNames = $parameterType->getObjectClassNames();
            if (\count($classNames) !== 1) {
                continue;
            }
            $className = $classNames[0];
            if ($this->reflectionProvider->hasClass($className) === false) {
                continue;
            }
            $parameterClass = $this->reflectionProvider->getClass($className);

            $availableInterfaces = \array_filter(
                $parameterClass->getInterfaces(),
                function (ClassReflection $interfaceReflection) {
                    return \in_array($interfaceReflection->getName(), $this->excludedInterfaces, true) === false;
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
                    $parameter->getName(),
                    $className,
                    \implode(', ', $interfaceNames),
                ))->identifier('pinkweb.constructor.preferInterface')->build();
            }
        }

        return $errors;
    }
}
