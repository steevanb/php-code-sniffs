#!/usr/bin/env bash
set -eu

set +e
tty -s && isInteractiveShell=true || isInteractiveShell=false
set -e

if ${isInteractiveShell}; then
    readonly DOCKER_RUN_PARAMETER_INTERACTIVE="--interactive"
else
    readonly DOCKER_RUN_PARAMETER_INTERACTIVE=
fi
