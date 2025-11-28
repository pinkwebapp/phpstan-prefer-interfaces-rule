SHELL := /bin/sh

.PHONY: test

# Run PHPUnit tests inside a PHP 8.4 CLI container
test:
	docker container run --rm \
	  -v $$(pwd):/app/ \
	  -w /app \
	  php:8.4-cli \
	  sh -lc "\
	    set -e; \
	    if [ ! -f vendor/bin/phpunit ]; then \
	      apt-get update && apt-get install -y --no-install-recommends unzip git && \
	      rm -rf /var/lib/apt/lists/*; \
	      php -r 'copy(\"https://getcomposer.org/installer\", \"composer-setup.php\");' && \
	      php composer-setup.php --2 --install-dir=/usr/local/bin --filename=composer && \
	      rm composer-setup.php && \
	      composer install --no-interaction --prefer-dist --no-progress; \
	    fi; \
	    vendor/bin/phpunit \
	  "
