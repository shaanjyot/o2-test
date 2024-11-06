<?php

define('OXYGEN_DOMAIN', 'http://localhost');

function oxygen_http_post($url, $arr) {
  $response = wp_remote_post(
    OXYGEN_DOMAIN . $url,
    array(
      'method'  => 'POST',
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body'    => json_encode($arr)
    )
  );
  $servBody = json_decode(wp_remote_retrieve_body($response), true);
  return $servBody;
}

function op_remove_dir($dir)
{
  if (file_exists($dir)) {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
        RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($dir);
    return true;
  }
  return false;
}
function op_remove_file($file)
{
  if (file_exists($file)) {
    unlink($file);
    return true;
  }
  return false;
}

function op_xcopy($source, $dest, $permissions = 0755)
{
    $sourceHash = op_hashDirectory($source);
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        if($sourceHash != op_hashDirectory($source."/".$entry)){
             op_xcopy("$source/$entry", "$dest/$entry", $permissions);
        }
    }

    // Clean up
    $dir->close();
    return true;
}

// In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated

function op_hashDirectory($directory){
    if (! is_dir($directory)){ return false; }

    $files = array();
    $dir = dir($directory);

    while (false !== ($file = $dir->read())){
        if ($file != '.' and $file != '..') {
            if (is_dir($directory . '/' . $file)) { $files[] = op_hashDirectory($directory . '/' . $file); }
            else { $files[] = md5_file($directory . '/' . $file); }
        }
    }

    $dir->close();

    return md5(implode('', $files));
}
function op_no_cache()
{
  if ( ! defined( 'DONOTCACHEPAGE' ) ) {
      define( 'DONOTCACHEPAGE', true );
  }
  if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
      define( 'DONOTCACHEOBJECT', true );
  }
  if ( ! defined( 'DONOTCACHEDB' ) ) {
      define( 'DONOTCACHEDB', true );
  }
}

include_once('auth.php');
include_once('grant.php');
include_once('channel.php');
include_once('role-api.php');
include_once('wp-plugin-api.php');
include_once('authentication.php');
include_once('mail.php');
include_once('cache-clean.php');
include_once('cronsync.php');
include_once('web-story.php');
