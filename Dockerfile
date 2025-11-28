# Project-specific development image for pinkweb/phpstan-prefer-interfaces-rule

FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --2 --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

WORKDIR /app

CMD ["php", "-v"]
