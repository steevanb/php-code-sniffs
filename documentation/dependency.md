## Installation as dependency

```bash
composer require --dev steevanb/php-code-sniffs ^4.5
```

## Usage

### Configuration file

Example of `phpcs.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="phpcs">
    <rule ref="vendor/steevanb/php-code-sniffs/src/Steevanb/ruleset.xml"/>
    <arg name="warning-severity" value="0"/>
    <exclude-pattern>/var</exclude-pattern>
    <exclude-pattern>/vendor</exclude-pattern>
    <arg name="report" value="steevanb\PhpCodeSniffs\Reports\Steevanb"/>
    <arg name="cache" value="var/ci/phpcs/cache"/>
    <file>.</file>
    <rule ref="Steevanb.Uses.GroupUses">
        <properties>
            <property name="firstLevelPrefixes" type="array">
                <element value="App"/>
            </property>
        </properties>
    </rule>
</ruleset>
```

Paths in this file are relatives to this file.

### Scan files

```bash
vendor/bin/phpcs \
    --standard=vendor/steevanb/php-code-sniffs/src/Steevanb/ruleset.xml \
    --report=steevanb\\PhpCodeSniffs\\Reports\\Steevanb \
    src/
```

Some phpcs parameters:
 * `-s`: show sniffer name
 * `--report-csv=foo.csv`: write report results in CSV
 * `--ignore=/vendor,/var`: ignore some directories or files
 * `-e`: show enabled coding standards
 * `--boostrap=/foo/file.php`: boostrap file to configure phpcs or init your code for example
 * `--warning-severity=0`: do not show warnings
 * `-p`: show progression

### Scan files need to be commited

```bash
git status --porcelain | grep -E '^[^D\?]{2} .*\.php$' | awk '{print $2}' | xargs -n1 vendor/bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report=steevanb\\PhpCodeSniffs\\Reports\\Steevanb
```

### Include this ruleset in your ruleset.xml

```xml
<?xml version="1.0"?>
<ruleset>
    <rule ref="vendor/steevanb/php-code-sniffs/src/Steevanb/ruleset.xml"/>
</ruleset>
```
