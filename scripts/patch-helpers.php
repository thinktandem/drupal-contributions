<?php

/**
 * @file
 * Get, apply, and revert patches.
 */
//print_r($argv);exit;
$url = $argv[1];
$urlParts = explode('/', $url);
$patchName = end($urlParts);
//print_r($url);
if (strpos($url, 'https') !== FALSE) {
  getPatch($url);
  movePatch($patchName);
  applyPatch($patchName);
}
elseif ($url === '--revert') {
  $patch = $argv[2];
  revertPatch($patch);
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
 *
 */
function movePatch($patchName) {
  exec(
    "mv $patchName web/"
  );
}

function applyPatch($patchName) {
  exec(
    "cd /app/web &&
    git apply -v $patchName"
  );
}

function revertPatch($patchName) {
  exec(
    "cd /app/web &&
    git apply -Rv $patchName"
  );
}
