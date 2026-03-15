FROM php:8.4.19-cli-alpine3.23

ARG VERSION

COPY --from=composer:2.9.5 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/php-code-sniffs

RUN \
    # zip is for Composer, git to clone repository
    apk add --no-cache zip git \
    && git clone --single-branch --branch=${VERSION} https://github.com/steevanb/php-code-sniffs.git . \
    && composer install --no-cache --classmap-authoritative

ENV PHPCS_PARAMETERS=""
ENV PHPCS_BOOTSTRAP=""

ENTRYPOINT ["/var/php-code-sniffs/docker/release/entrypoint.sh"]
