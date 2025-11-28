SHELL := /bin/sh

IMAGE := pinkweb-phpstan-prefer-interfaces-rule:8.2-dev

RUN := docker run --rm --user $(shell id -u):$(shell id -g) -v .:/app -w /app $(IMAGE) sh -lc

.PHONY: build test stan cs-fix

# Build the development image containing all required tools
build:
	docker build -t $(IMAGE) . \
		&& $(RUN) "composer install --dev --no-interaction --prefer-dist --no-progress" \
		&& $(RUN) "composer clear-cache" && $(RUN) "composer dump-autoload -o"

# Run PHPUnit tests
test: build
	$(RUN) "vendor/bin/phpunit"

# Run PHPStan analysis
stan: build
	$(RUN) "vendor/bin/phpstan analyse src --no-progress --memory-limit=1G"

# Run PHP-CS-Fixer
cs-fix: build
	$(RUN) "vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --allow-risky=yes || true"
