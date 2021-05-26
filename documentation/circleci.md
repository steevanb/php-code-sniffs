# Add phpcs to CircleCI

```yaml
# .circleci/config.yml
version: '2.1'

jobs:
    phpcs:
        docker:
            - image: steevanb/php-code-sniffs:4.2.0
        working_directory: /app
        steps:
            - checkout
            - run:
                name: phpcs
                command: /var/php-code-sniffs/docker/entrypoint.sh

workflows:
    version: '2.1'
    CI:
        jobs:
            - phpcs
```

# Configure your code styles

To use your own code styles, you can create [bin/phpcs](docker.md) and use it with CircleCI:

```yaml
# .circleci/config.yml
version: '2.1'

jobs:
    phpcs:
        docker:
            - image: steevanb/php-code-sniffs:4.2.0
        working_directory: /app
        steps:
            - checkout
            - run:
                name: phpcs
                command: /app/bin/phpcs

workflows:
    version: '2.1'
    CI:
        jobs:
            - phpcs
```
