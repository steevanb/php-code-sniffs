# Run phpcs with Docker

You can run phpcs without installing it as dependency with Docker:

```bash
docker \
    run \
        -it \
        --rm \
        -v /path/to/my/project:/app:ro \
        steevanb/php-code-sniffs:6.0.0
```

All files in `/app` into Docker container directory will be tested.

## Environment variables

| Variable | Description | Default |
|----------|-------------|---------|
| `PHPCS_PARAMETERS` | Extra CLI arguments for phpcs | `""` |
| `PHPCS_BOOTSTRAP` | Bootstrap file path | `""` |

## Create a binary file to run it

You can create a binary `bin/phpcs` to run phpcs Docker image.

Example of a binary file to run phpcs in Docker or just run phpcs if already in a container:
```bash
#!/usr/bin/env bash

set -eu

readonly PROJECT_DIRECTORY=$(realpath $(dirname $(realpath $0))/..)

if [ "$(which docker || false)" ]; then
    docker \
        run \
            -it \
            --rm \
            -v ${PROJECT_DIRECTORY}:/app:ro \
            --entrypoint /app/bin/phpcs \
            steevanb/php-code-sniffs:6.0.0
else
    # Add parameters to phpcs (not mandatory)
    PHPCS_PARAMETERS="-p --warning-severity=0 --ignore=vendor/"
    # Configure your bootstrap file (not mandatory)
    PHPCS_BOOTSTRAP="bootstrap.php"

    # Run phpcs
    /var/php-code-sniffs/docker/release/entrypoint.sh
fi
```
