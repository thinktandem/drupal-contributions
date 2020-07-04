<?php

/**
 * @file
 * Get, apply, and revert patches.
 */
switch ($argv[1]) {
  case '--revert':
    $patch = $argv[2];
    revertPatch($patch);
    break;

  case '--create-patch':
    createPatch();
    break;

  default:
    $url = $argv[1];
    if (strpos($url, 'https') !== FALSE) {
      $urlParts = explode('/', $url);
      $patchName = end($urlParts);
      getPatch($url);
      applyPatch($patchName);
    }
}

/**
 * Get a patch from drupal.org issue.
 *
 * @param string $url
 *   The URL of the patch.
 */
function getPatch($url) {
  exec(
    "wget -P /app $url"
  );
}

/**
 * Apply a patch.
 *
 * @param string $patchName
 *   The name of the patch to apply.
 */
function applyPatch($patchName) {
  exec(
    "git -C /app/web apply -v /app/$patchName"
  );
}

/**
 * Revert a patch.
 *
 * @param string $patchName
 *   The name of the patch to revert.
 */
function revertPatch($patchName) {
  exec(
    "git -C /app/web apply -Rv /app/$patchName"
  );
}

/**
 * Create a patch from the committed changes on your local branch.
 */
function createPatch() {
  exec("git -C /app/web symbolic-ref HEAD", $output);
  $branch = explode('/', $output[0]);
  $branch = end($branch);
  exec(
    "git -C /app/web diff 8.8.x > /app/$branch.patch"
  );
}
