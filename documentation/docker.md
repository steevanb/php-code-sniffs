# Run phpcs with Docker

You can run phpcs without installing it as dependency with Docker:

```bash
docker \
    run \
        -it \
        --rm \
        -v /path/to/my/project:/app:ro \
        steevanb/php-code-sniffs:4.2.0
```

All files in `/app` into Docker container directory will be tested.

# Create a binary file to run it

You can create a binary `bin/phpcs` to run phpcs Docker image.

Example of a binary file to run phpcs in Docker or just run phpcs if already in a container (for [CircleCI](circleci.md)):
```bash
#!/usr/bin/env sh

set -eu

readonly PROJECT_DIRECTORY=$(realpath $(dirname $(realpath $0))/..)

if [ $(which docker || false) ]; then
    docker \
        run \
            -it \
            --rm \
            -v ${PROJECT_DIRECTORY}:/app:ro \
            --entrypoint /app/bin/phpcs \
            steevanb/php-code-sniffs:4.2.0
else
    # Add parameters to phpcs
    PHPCS_PARAMETERS="-p --warning-severity=0 --ignore=vendor/"
    # Configure your bootstrap file
    PHPCS_BOOTSTRAP="bootstrap.php"

    # Run phpcs
    /var/php-code-sniffs/docker/entrypoint.sh
fi
```

