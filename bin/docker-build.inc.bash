#!/usr/bin/env bash

set -eu

function buildDockerImage() {
    local dockerBuildArguments=""

    if ${refresh}; then
        dockerBuildArguments="--no-cache --pull"
    fi

    echo "Building Docker image ${DOCKER_IMAGE_NAME}..."
    DOCKER_BUILDKIT=1 \
        docker \
            build \
                "${DOCKERFILE_PATH}" \
                --tag "${DOCKER_IMAGE_NAME}" \
                ${dockerBuildArguments} \
                ${DOCKER_BUILD_EXTRA_ARGUMENTS:-}
}

function pushDockerImage() {
    echo "Pushing Docker image ${DOCKER_IMAGE_NAME}..."
    docker push "${DOCKER_IMAGE_NAME}"
}

refresh=false
push=false
for param in "${@}"; do
    if [ "${param}" == "--refresh" ]; then
        refresh=true
    elif [ "${param}" == "--push" ]; then
        push=true
    fi
done

buildDockerImage
if ${push}; then
    pushDockerImage
fi
