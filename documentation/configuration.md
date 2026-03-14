# Configure sniffs

Some sniffs can be configured in xml.

The report `steevanb\PhpCodeSniffs\Reports\Steevanb` can be configured with a bootstrap file.

# Configuration with xml

### GroupUsesSniff

Configure namespace prefixes that must be grouped when multiple `use` statements share the same prefix:
```xml
<rule ref="Steevanb.Uses.GroupUses">
    <properties>
        <property name="groupPrefixes" type="array">
            <element value="App\Foo"/>
            <element value="Symfony\Component\HttpFoundation"/>
        </property>
    </properties>
</rule>
```

### CamelCapsFunctionNameSniff

Allow some method names to not follow the camelCase convention (e.g. for external libraries like Doctrine):
```xml
<rule ref="Steevanb.NamingConventions.CamelCapsFunctionName">
    <properties>
        <property name="allowedNotCamelCase" type="array">
            <element value="getSQLDeclaration"/>
            <element value="convertToPHPValue"/>
            <element value="requiresSQLCommentHint"/>
        </property>
    </properties>
</rule>
```

### ValidVariableNameSniff

Allow some variable names to not follow the camelCase convention:
```xml
<rule ref="Steevanb.NamingConventions.ValidVariableName">
    <properties>
        <property name="allowedVariableNames" type="array">
            <element value="my_variable"/>
        </property>
    </properties>
</rule>
```

### NestingLevelSniff

Allow some methods to exceed the nesting level limit:
```xml
<rule ref="Steevanb.Metrics.NestingLevel">
    <properties>
        <property name="allowedNestingLevelMethods" type="array">
            <element value="foo.php::barMethod"/>
        </property>
    </properties>
</rule>
```

### DeprecatedFunctionsSniff

Allow some deprecated functions to be used:
```xml
<rule ref="Steevanb.Php.DeprecatedFunctions">
    <properties>
        <property name="allowedDeprecatedFunctions" type="array">
            <element value="deprecated_function"/>
        </property>
    </properties>
</rule>
```

### GroupUsesSniff presets

Pre-configured rulesets are available for Symfony and Doctrine namespaces in the `rulesets/` directory:
```xml
<rule ref="vendor/steevanb/php-code-sniffs/rulesets/symfony.xml"/>
<rule ref="vendor/steevanb/php-code-sniffs/rulesets/doctrine.xml"/>
```

You can combine them with your own prefixes:
```xml
<rule ref="vendor/steevanb/php-code-sniffs/rulesets/symfony.xml"/>
<rule ref="vendor/steevanb/php-code-sniffs/rulesets/doctrine.xml"/>
<rule ref="Steevanb.Uses.GroupUses">
    <properties>
        <property name="groupPrefixes" type="array">
            <element value="App"/>
        </property>
    </properties>
</rule>
```

# Create a bootstrap file

You can add a bootstrap file to phpcs to configure sniffs:

```php
# phpcs_boostrap.php

// If you use the Docker image,
// file path must not be the same between Docker and your local file system.
// You can change a part of the path to files who have errors, to make file:// works in bash.
steevanb\PhpCodeSniffs\Reports\Steevanb::addReplaceInPath('/app', __DIR__);

```

# Add your bootstrap file to phpcs

## phpcs installed as dependency

```bash
vendor/bin/phpcs --bootstrap=phpcs_boostrap.php (...)
```

## phpcs called with Docker image

```bash
docker run -e PHPCS_BOOTSTRAP=phpcs_boostrap.php (...)
```
