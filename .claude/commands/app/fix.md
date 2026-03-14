Execute the following steps to fix code style issues in this project:

1. Run `bin/ci/phpcbf` to auto-fix what can be fixed. This command may return a non-zero exit code even on success (exit code 1 means some files were fixed, exit code 2 means errors remain). Do not stop on non-zero exit codes.

2. Run `bin/ci/phpcs` to check remaining errors. If there are no errors, stop here.

3. For each remaining error reported by phpcs, read the file, understand the error, and fix it. Common errors include:
   - Member ordering (public before protected before private, constants before properties before methods)
   - Line length exceeding 120 characters
   - Any other coding standard violation from the Steevanb standard

4. After fixing, run `bin/ci/phpcs` again to confirm all errors are resolved. Repeat step 3 if needed until phpcs passes with 0 errors.
