FROM php:8.1.2-cli-alpine3.15

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/php-code-sniffs

RUN \
    # zip is for Composer, git to clone repository and for CircleCI and openssh for CircleCI
    apk add --no-cache zip git openssh \
    && git clone --single-branch --branch=5.0.0 https://github.com/steevanb/php-code-sniffs.git . \
    && composer install --no-cache --classmap-authoritative

ENV PHPCS_PARAMETERS=""
ENV PHPCS_BOOTSTRAP=""

ENTRYPOINT ["/var/php-code-sniffs/docker/entrypoint.sh"]
