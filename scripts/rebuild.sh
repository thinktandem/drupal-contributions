#!/bin/bash

drush --root=/app/web si --db-url=mysql://drupal8:drupal8@database/drupal8 -y
drush --root=/app/web en simpletest -y
mkdir -p -m 777 /app/web/sites/simpletest/browser_output
find /app/web/sites/default -type d -exec chmod 777 '{}' \;
drush --root=/app/web --uri=https://drupal-contributions.lndo.site uli
echo "vendor" >> /app/web/.gitignore
echo ".gitignore\nsites/simpletest" >> /app/web/.gitignore
echo "sites/default/files" >> /app/web/.gitignore
echo "sites/default/settings.php" >> /app/web/.gitignore
