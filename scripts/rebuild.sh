#!/bin/sh

/app/web/vendor/drush/drush/drush --root=/app/web si --db-url=mysql://drupal9:drupal9@database/drupal9 -y
mkdir -p -m 777 /app/web/sites/simpletest/browser_output
find /app/web/sites/default -type d -exec chmod 777 '{}' \;
/app/web/vendor/drush/drush/drush --root=/app/web --uri=https://drupal-contributions.lndo.site uli
echo "vendor" >> /app/web/.gitignore
echo ".gitignore\nsites/simpletest" >> /app/web/.gitignore
echo "sites/default/files" >> /app/web/.gitignore
echo "sites/default/settings.php" >> /app/web/.gitignore
