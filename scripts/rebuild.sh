#!/bin/bash

drush --root=/app/web si --db-url=mysql://drupal8:drupal8@database/drupal8 -y
drush --root=/app/web en simpletest -y
chmod -R 777 /app/web/sites/default
drush --root=/app/web --uri=https://drupal-contributions.lndo.site uli
echo "vendor" >> /app/web/.gitignore
echo ".gitignore\nsites/simpletest" >> /app/web/.gitignore
echo "sites/default/files" >> /app/web/.gitignore
echo "sites/default/settings.php" >> /app/web/.gitignore
