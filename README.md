PhpCS
=====

Liste des normes de dev qui seront vérifiées
--------------------------------------------

```bash
vendor/bin/phpcs --standard=./ruleset.xml -e
```

Vérifier les normes de dev des fichiers à commiter
--------------------------------------------------

```bash
git status --porcelain | grep -E '^[^D\?]{2} .*\.php$' | awk '{print $2}' | xargs -n1 bin/phpcs --standard=vendor/info-droid/phpcs/ruleset.xml
```

Vérifier les normes de dev de tous les fichiers d'un répertoire
---------------------------------------------------------------

```bash
vendor/bin/phpcs --standard=./ruleset.xml --report=InfoDroid src/

# écrire le résultat dans un fichier CSV
vendor/bin/phpcs --standard=vendor/info-droid/phpcs/ruleset.xml --report-csv=foo.csv src/

# lister les sniffs configurés
vendor/bin/phpcs --standard=vendor/info-droid/phpcs/ruleset.xml -e
```
