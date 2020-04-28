#!/usr/bin/env sh

set -e

appDir="/var/phpcs"
if [ -d /app ]; then
    appDir="/app"
fi

if [ -z ${PHPCS_BOOTSTRAP} ]; then
    bootstrap=""
else
    bootstrap="--bootstrap=${PHPCS_BOOTSTRAP}"
fi

/var/php-code-sniffs/vendor/bin/phpcs \
    ${bootstrap} \
    --standard=/var/php-code-sniffs/src/Steevanb/ruleset.xml \
    --report=steevanb\\PhpCodeSniffs\\Reports\\Steevanb \
    ${PHPCS_PARAMETERS} \
    ${appDir}
