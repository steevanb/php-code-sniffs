FROM php:8.4.19-cli-alpine3.23

COPY --from=composer:2.9.5 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_HOME=/composer
ENV COMPOSER_CACHE_DIR=/app/var/composer

COPY composer.json ${COMPOSER_HOME}/composer.json

RUN \
    apk add --no-cache bash shellcheck icu-dev \
    && docker-php-ext-install bcmath intl pcntl \
    && composer global update \
    && ln -s ${COMPOSER_HOME}/vendor/bin/composer-require-checker /usr/local/bin/composer-require-checker \
    && ln -s ${COMPOSER_HOME}/vendor/bin/phpstan /usr/local/bin/phpstan \
    && ln -s ${COMPOSER_HOME}/vendor/bin/unused_scanner /usr/local/bin/unused-scanner

COPY php.ini ${PHP_INI_DIR}/conf.d/ci.ini

WORKDIR /app
