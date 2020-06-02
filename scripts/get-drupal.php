<?php

/**
 * @file
 * Get Drupal source code if we haven't already.
 */

$haveDrupal = file_exists(__DIR__ . '../web') && is_dir(__DIR__ . '../web');

if ($haveDrupal) {
  print "\n\t\t\033[033m ... We already have Drupal ...\n";
}
else {
  exec(
    'git clone --branch 8.8.x https://git.drupalcode.org/project/drupal.git web'
  );
}
