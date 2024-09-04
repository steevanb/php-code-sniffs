# Configure sniffs

Some sniffs could be configured by static methods,
like `steevanb\PhpCodeSniffs\Steevanb\Sniffs\Metrics\NestingLevelSniff`
or the report `steevanb\PhpCodeSniffs\Reports\Steevanb`.

Some other sniffs can be configured in xml.

# Configuration with xml

### GroupUseSniff

```xml
<rule ref="Steevanb.Uses.GroupUses">
    <properties>
        <property name="firstLevelPrefixes" type="array">
            <element value="Foo"/>
        </property>
    </properties>
    
    <properties>
        <property name="thirdLevelPrefixes" type="array">
            <element value="Foo\Bar\"/>
        </property>
    </properties>
    
    <properties>
        <property name="fourthLevelPrefixes" type="array">
            <element value="Foop\Bar\Baz\"/>
        </property>
    </properties>
</rule>
```

Example for a project who use Symfony:
```xml
<rule ref="Steevanb.Uses.GroupUses">
    <properties>
        <property name="firstLevelPrefixes" type="array">
            <element value="App"/>
        </property>
    </properties>
    
    <properties>
        <property name="thirdLevelPrefixes" type="array">
            <element value="Symfony\Component"/>
            <element value="Symfony\Contracts"/>
            <element value="Symfony\Bundle"/>
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

// Add methods who could have a nesting level greater than 5.
steevanb\PhpCodeSniffs\Steevanb\Sniffs\Metrics\NestingLevelSniff::addAllowedNestingLevelMethods('foo.php', 'barMethod');

// Allow some deprecated function
steevanb\PhpCodeSniffs\Steevanb\Sniffs\PHP\DeprecatedFunctionsSniff::addAllowDeprecatedFunction('deprecated_function');
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
