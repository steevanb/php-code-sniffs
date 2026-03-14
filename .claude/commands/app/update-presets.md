Update the GroupUses ruleset presets in `rulesets/symfony.xml` and `rulesets/doctrine.xml` with the latest namespaces.

## Symfony

1. Fetch `https://api.github.com/repos/symfony/symfony/contents/src/Symfony` to get the top-level categories (Bridge, Bundle, Component, Contracts).

2. For each category, fetch its contents via `https://api.github.com/repos/symfony/symfony/contents/src/Symfony/<Category>` to get sub-directories.

3. Skip non-directory entries and entries starting with `.` (like `.github`).

4. Each sub-directory becomes a prefix: `Symfony\<Category>\<SubDirectory>`.

5. Write all prefixes to `rulesets/symfony.xml`, grouped by category with XML comments, sorted alphabetically within each category.

## Doctrine

1. Fetch `https://api.github.com/search/repositories?q=org:doctrine+archived:false&per_page=100` to get all active Doctrine repositories.

2. For each repository, fetch its composer.json via `https://api.github.com/repos/doctrine/<repo-name>/contents/composer.json` and extract the PSR-4 autoload namespace prefix.

3. Skip repositories where the composer.json cannot be fetched or has no PSR-4 autoload.

4. Remove trailing backslashes and deduplicate namespace prefixes.

5. Write all prefixes to `rulesets/doctrine.xml`, sorted alphabetically.

## Output format

Both files must follow this XML structure:
```xml
<?xml version="1.0"?>
<ruleset name="SteevanB <Name>">
    <description>GroupUses configuration for <Name> namespaces</description>

    <rule ref="Steevanb.Uses.GroupUses">
        <properties>
            <property name="groupPrefixes" type="array">
                <element value="Namespace\Prefix"/>
            </property>
        </properties>
    </rule>
</ruleset>
```

After updating both files, report what changed (added/removed prefixes).
