<?php

/**
 * @file
 * Get Drupal source code if we haven't already.
 */

exec(
  'git clone --branch 8.8.x https://git.drupalcode.org/project/drupal.git web'
);
