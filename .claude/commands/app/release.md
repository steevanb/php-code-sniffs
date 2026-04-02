Prepare the release of a new version of the library. The version is passed as argument: $ARGUMENTS. If no version is provided, ask the user for it. Never guess the version.

Execute the following steps:

1. Update `README.md`:
   - Replace the version badge: `version-<OLD>-green.svg` becomes `version-<NEW>-green.svg`
   - Replace the badge link: `tree/<OLD>` becomes `tree/<NEW>`
   - Update the code lines badge: count all lines in the repository excluding `var/` and `vendor/` directories (use `find . -type f -not -path './var/*' -not -path './vendor/*' | xargs wc -l`), then replace `code%20lines-<OLD_COUNT>-green.svg` with `code%20lines-<NEW_COUNT>-green.svg`. Format the count with "," as thousands separator (for example `14,808`)

2. Update `documentation/changelog.md`:
   - Find the `### master` section at the top of the file
   - Rename it to `### [<NEW>](../../../compare/<PREVIOUS>...<NEW>)` where `<PREVIOUS>` is the version from the next section below (the most recent released version)
   - Add a new empty `### master` section at the very top of the file, above the newly renamed section

3. Update version in `documentation/circleci.md`:
   - Replace all occurrences of `steevanb/php-code-sniffs:<OLD>` with `steevanb/php-code-sniffs:<NEW>`

4. Update version in `documentation/dependency.md`:
   - Replace `steevanb/php-code-sniffs ^<OLD_MAJOR>.<OLD_MINOR>` with `steevanb/php-code-sniffs ^<NEW_MAJOR>.<NEW_MINOR>`

5. Update version in `documentation/docker.md`:
   - Replace all occurrences of `steevanb/php-code-sniffs:<OLD>` with `steevanb/php-code-sniffs:<NEW>`

6. Update the sniff list in `documentation/sniffs.md`:
   - In the `## steevanb/php-code-sniffer` section, regenerate the table of custom sniffs
   - Scan all `*Sniff.php` files in `src/Steevanb/Sniffs/` to build the list
   - Each sniff name follows the format `Steevanb.<Category>.<Name>` where `<Category>` is the subdirectory name and `<Name>` is the class name without the `Sniff` suffix
   - Each row links to the sniff source on GitHub: `[Steevanb.<Category>.<Name>](https://github.com/steevanb/php-code-sniffs/blob/master/src/Steevanb/Sniffs/<Category>/<Name>Sniff.php)`
   - Sort sniffs alphabetically
   - Do not modify the `## PHPCSStandards/PHP_CodeSniffer` section

After all updates, report the list of files modified and the changes made.
