#!/bin/bash

drush --root=/app/web si --db-url=mysql://drupal8:drupal8@database/drupal8 -y
chmod -R 777 /app/web/sites/default
drush --root=/app/web --uri=https://drupal-contributions.lndo.site uli
