<?php

namespace Salsadigitalauorg\ScaffoldTesting\Installer;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
  /**
   * Handles the copying of feature files and FeatureContext.php to the specified directories in the consuming project.
   * @param Event $event Composer script event
   */
  public static function features(Event $event)
  {
    $io = $event->getIO();
    $io->write('Installer::features method called');

    $composer = $event->getComposer();
    $extras = $composer->getPackage()->getExtra();

    $io->write('Extras: ' . json_encode($extras));

    if (!isset($extras['scaffold-testing'])) {
      $io->write('No scaffold-testing configuration found');
      return;
    }

    $config = $extras['scaffold-testing'];
    $io->write('Config: ' . json_encode($config));

    // Access extra configurations in composer.json
    $extras = $composer->getPackage()->getExtra();

    if (!isset($extras['scaffold-testing'])) {
      $io->writeError("No 'scaffold-testing' configuration found in composer.json.");
      return;
    }

    $config = $extras['scaffold-testing'];
    $targetDir = rtrim($config['target-dir'] ?? 'tests/behat/', '/') . '/';
    $files = $config['files'] ?? [];
    $override_feature = $config['override_feature'] ?? false;
    $override_feature_context = $config['override_feature_context'] ?? false;

    // Ensuring the target directories exist
    $targetPath = getcwd() . '/' . $targetDir;
    $featurePath = $targetPath . 'features/';
    $bootstrapPath = $targetPath . 'bootstrap/';
    
    self::createDirectory($io, $targetPath);
    self::createDirectory($io, $featurePath);
    self::createDirectory($io, $bootstrapPath);

    // Process each feature file
    foreach ($files as $file) {
      $sourcePath = __DIR__ . '/../../features/' . $file;
      $destPath = $featurePath . $file;

      self::copyFile($io, $sourcePath, $destPath, $override_feature, "feature");
    }

    // Process FeatureContext.php
    $contextSourcePath = __DIR__ . '/../../bootstrap/FeatureContext.php';
    $contextDestPath = $bootstrapPath . 'FeatureContext.php';
    
    self::copyFile($io, $contextSourcePath, $contextDestPath, $override_feature_context, "FeatureContext.php");
  }

  /**
   * Creates a directory if it doesn't exist.
   */
  private static function createDirectory($io, $path)
  {
    if (!is_dir($path) && !mkdir($path, 0777, true)) {
      $io->writeError("Failed to create directory: $path");
    }
  }

  /**
   * Copies a file if conditions are met.
   */
  private static function copyFile($io, $sourcePath, $destPath, $override, $fileType)
  {
    if (file_exists($destPath) && !$override) {
      $io->write("Skipping $fileType file because it already exists and 'override_$fileType' is set to false.");
      return;
    }

    if (!copy($sourcePath, $destPath)) {
      $io->writeError("Failed to install $fileType file to " . dirname($destPath));
    } else {
      $io->write("Installed $fileType file to " . dirname($destPath));
    }
  }
}
