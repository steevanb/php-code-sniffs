#!/usr/bin/env bash

set -eu

if ! command -v docker &> /dev/null; then
    isInDocker=true
else
    isInDocker=false
fi

if ${isInDocker}; then
    return 0
fi

set +e
tty -s && dockerRunParameterInteractive="--interactive" || dockerRunParameterInteractive=
set -e

readonly BIN_DIR="${BIN_DIR:-bin/ci}"

docker \
    run \
        --rm \
        --tty \
        ${dockerRunParameterInteractive} \
        --volume "${ROOT_PATH}":/app \
        --workdir /app \
        --user "$(id -u)":"$(id -g)" \
        --entrypoint /app/"${BIN_DIR}"/"$(basename "$0")" \
        "${DOCKER_IMAGE_NAME:-${CI_DOCKER_IMAGE_NAME}}" \
            "${@}"

exit 0
