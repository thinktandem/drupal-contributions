<?php

/**
 * @file
 * Get, apply, and revert patches.
 */
$url = $argv[1];
$urlParts = explode('/', $url);
$patchName = end($urlParts);
if (strpos($url, 'https') !== FALSE) {
  getPatch($url);
  movePatch($patchName);
  applyPatch($patchName);
}
elseif ($url === '--revert') {
  $patch = $argv[2];
  revertPatch($patch);
}
elseif ($url === '--create-patch') {
  createPatch();
}

/**
 * Get a patch from drupal.org issue.
 *
 * @param string $url
 *   The URL of the patch.
 */
function getPatch($url) {
  exec(
    "wget $url"
  );
}

/**
 * Move the patch into drupal root.
 *
 * @param string $patchName
 *   The name of the patch.
 */
function movePatch($patchName) {
  exec(
    "mv $patchName web/"
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
    "cd /app/web &&
    git apply -v $patchName"
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
    "cd /app/web &&
    git apply -Rv $patchName"
  );
}

/**
 * Create a patch from the committed changes on your local branch.
 */
function createPatch() {
  exec("cd /app/web && git symbolic-ref HEAD", $output);
  $branch = explode('/', $output[0]);
  $branch = end($branch);
  exec(
    "cd /app/web &&
    git diff 8.8.x > $branch.patch"
  );
}
