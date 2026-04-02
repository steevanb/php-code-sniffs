# Development

## Test phpcs on an external project

Run phpcs on an external project while using the local source code of php-code-sniffs:

```bash
docker run -it --rm -v $(pwd):/app:ro -v /path/to/php-code-sniffs/src:/var/php-code-sniffs/src:ro steevanb/php-code-sniffs:6.1.2
```

This mounts your external project into `/app` and overrides the sniff source code inside the container with your local version, allowing you to test changes without rebuilding the image.

To use the project's `phpcs.xml` configuration file:

```bash
docker run -it --rm -v $(pwd):/app:ro -v /path/to/php-code-sniffs/src:/var/php-code-sniffs/src:ro -e PHPCS_PARAMETERS="--standard=/app/phpcs.xml" steevanb/php-code-sniffs:6.1.2
```

If phpcs is installed in `/composer/vendor/steevanb/php-code-sniffs` (CI Docker image):

```bash
docker run -it --rm -v $(pwd):/app -v /home/infodroid/dev/steevanb/php-code-sniffs:/composer/vendor/steevanb/php-code-sniffs:ro  -e PHPCS_PARAMETERS="--standard=/app/config/ci/phpcs.xml" steevanb/php-code-sniffs:6.1.2
```
