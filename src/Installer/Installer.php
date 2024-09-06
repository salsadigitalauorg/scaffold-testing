<?php

namespace Salsadigitalauorg\ScaffoldTesting\Installer;;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
  /**
   * Handles the copying of feature files to the specified directory in the consuming project.
   * @param Event $event Composer script event
   */
  public static function features(Event $event)
  {
    $io = $event->getIO();
    $composer = $event->getComposer();

    // Access extra configurations in composer.json
    $extras = $composer->getPackage()->getExtra();

    if (!isset($extras['scaffold-testing'])) {
      $io->writeError("No 'scaffold-testing' configuration found in composer.json.");
      return;
    }

    $config = $extras['scaffold-testing'];
    $targetDir = rtrim($config['target-dir'], '/') . '/';
    $files = $config['files'] ?? [];
    $override = $config['override'] ?? false;

    // Ensuring the target directory exists
    $targetPath = getcwd() . '/' . $targetDir;
    if (!is_dir($targetPath)) {
      if (!mkdir($targetPath, 0777, true)) {
        $io->writeError("Failed to create target directory: $targetPath");
        return;
      }
    }

    // Process each file
    foreach ($files as $file) {
      $sourcePath = __DIR__ . '/../../features/' . $file;
      $destPath = $targetPath . $file;

      // Check if file exists and override is false
      if (file_exists($destPath) && !$override) {
        $io->write("Skipping $file because it already exists and 'override' is set to false.");
        continue;
      }

      // Attempt to copy the file
      if (!copy($sourcePath, $destPath)) {
        $io->writeError("Failed to install $file to $targetDir");
      } else {
        $io->write("Installed $file to $targetDir");
      }
    }
  }
}
