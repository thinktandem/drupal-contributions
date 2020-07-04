<?php

/**
 * @file
 * Get Drupal source code if we haven't already.
 */

include '/app/config/drupal-branch.php';

exec(
  "git clone --branch $drupalBranch https://git.drupalcode.org/project/drupal.git web"
);
