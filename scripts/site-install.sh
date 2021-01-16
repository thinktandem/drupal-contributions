#!/bin/sh

/app/web/vendor/drush/drush/drush --root=/app/web si --db-url=mysql://drupal9:drupal9@database/drupal9 -y $1
find /app/web/sites/default -type d -exec chmod 777 '{}' \;
/app/web/vendor/drush/drush/drush --root=/app/web --uri=https://drupal-contributions.lndo.site uli
