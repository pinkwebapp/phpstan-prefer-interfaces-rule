PHPStan Prefer Interfaces Rule
================================

Custom PHPStan rule that encourages using interfaces instead of concrete classes as constructor arguments. If a
constructor parameter is a concrete class that implements one or more interfaces (not excluded), the rule reports an
error and suggests the available interfaces.



Requirements
------------

- PHP: ^7.4 or ^8.0
- PHPStan: ^2

Installation
------------

Install the package as a dev dependency via Composer:

```
composer require --dev pinkweb/phpstan-prefer-interfaces-rule
```

Usage
-----

Enable the rule by including the provided config in your PHPStan configuration file (e.g. `phpstan.neon` or
`phpstan.neon.dist`):

```
includes:
    - vendor/pinkweb/phpstan-prefer-interfaces-rule/rules.neon
```

The bundled `rules.neon` registers the rule and provides a default list of interfaces that are excluded from reporting (
e.g. `DateTimeInterface`, `Stringable`, SPL interfaces, PSR Logger, etc.).

Configuring `excludedInterfaces`
--------------------------------

You can control which interfaces are ignored by defining the `excludedInterfaces` parameter in your own PHPStan config.
Later definitions override earlier ones, so add your configuration after the `includes` entry.

- Overwrite the defaults completely:

```
includes:
    - vendor/pinkweb/phpstan-prefer-interfaces-rule/rules.neon

parameters:
    excludedInterfaces:
        - DateTimeInterface
        - Psr\Log\LoggerInterface
        - Your\Custom\Interface
```

- Extend the defaults (copy the defaults from the package and add your own entries):

```
includes:
    - vendor/pinkweb/phpstan-prefer-interfaces-rule/rules.neon

parameters:
    excludedInterfaces:
        # Defaults from the package (keep or adjust as desired)
        - DateTimeInterface
        - Stringable
        - Countable
        - Iterator
        - IteratorAggregate
        - ArrayAccess
        - Traversable
        - BackedEnum
        - UnitEnum
        - Throwable
        - JsonSerializable
        - Serializable
        - Psr\Log\LoggerInterface
        # Your additions
        - Your\Additional\Interface
```

Note: PHPStan config does not deep-merge arrays by default. Defining `parameters.excludedInterfaces` in your project
replaces the one from the included file, so include the full list you want to use.

What the error looks like
-------------------------

When a constructor parameter uses a concrete class where an interface should be preferred, the rule emits an error
similar to:

```
Constructor argument #0 "$service" is of type App\ConcreteService but should be one of: App\Contracts\ServiceInterface
```

Error identifier: `pinkweb.constructor.preferInterface`.

Testing
-------

This repository includes a Makefile to run tests in a Dockerized PHP 8.4 CLI environment.

- Run the test suite:

```
make test
```

This uses `php:8.4-cli`, bootstraps Composer inside the container, installs required tools (`unzip`, `git`), installs
dependencies, and executes PHPUnit.
