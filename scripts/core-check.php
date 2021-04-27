<?php

/**
 * @file
 * A wrapper for core/scripts/dev/commit-code-check.sh using the configured
 * branch.
 */

include '/app/config/drupal-branch.php';

exec(
  "cd /app/web &&
  bash /app/web/core/scripts/dev/commit-code-check.sh --branch $drupalBranch"
);
