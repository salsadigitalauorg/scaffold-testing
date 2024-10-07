<?php

namespace Salsadigitalauorg\ScaffoldTesting\Installer;

use Composer\Script\Event;

class Installer
{
    public static function features(Event $event)
    {
        $io = $event->getIO();
        $io->write('Installer::features method called');

        $composer = $event->getComposer();
        $extras = $composer->getPackage()->getExtra();

        if (!isset($extras['scaffold-testing'])) {
            $io->write('No scaffold-testing configuration found');
            return;
        }

        $config = $extras['scaffold-testing'];
        $targetDir = rtrim($config['target-dir'] ?? 'tests/behat/', '/') . '/';
        $override_feature_context = $config['override_feature_context'] ?? false;

        $targetPath = getcwd() . '/' . $targetDir;
        $featurePath = $targetPath . 'features/';
        $bootstrapPath = $targetPath . 'bootstrap/';

        self::createDirectory($io, $featurePath);
        self::createDirectory($io, $bootstrapPath);

        $sourceDir = __DIR__ . '/../../tests/behat/features/';
        
        if (!isset($config['files']) || empty($config['files'])) {
            // If 'files' is not set or empty, get all .feature files from the source directory
            $files = array_map('basename', glob($sourceDir . '*.feature'));
            $files = array_fill_keys($files, false);  // Set override to false for all files
        } else {
            // Convert the list of files to an associative array with override set to false
            $files = array_fill_keys($config['files'], false);
        }

        foreach ($files as $file => $override) {
            $sourcePath = $sourceDir . $file;
            $destPath = $featurePath . $file;

            if (file_exists($sourcePath)) {
                if (!file_exists($destPath)) {
                    if (copy($sourcePath, $destPath)) {
                        $io->write("Installed $file file to " . dirname($destPath));
                    } else {
                        $io->writeError("Failed to install $file file to " . dirname($destPath));
                    }
                } else if ($override) {
                    if (copy($sourcePath, $destPath)) {
                        $io->write("Updated $file file in " . dirname($destPath));
                    } else {
                        $io->writeError("Failed to update $file file in " . dirname($destPath));
                    }
                } else {
                    $io->write("Skipped $file file as it already exists and override is set to false.");
                }
            } else {
                $io->writeError("Source file not found: $file");
            }
        }

        $contextSourcePath = __DIR__ . '/../../tests/behat/bootstrap/FeatureContext.php';
        $contextDestPath = $bootstrapPath . 'FeatureContext.php';

        if (file_exists($contextSourcePath)) {
            if (!file_exists($contextDestPath) || $override_feature_context) {
                if (copy($contextSourcePath, $contextDestPath)) {
                    $io->write("Installed FeatureContext.php file to $bootstrapPath");
                } else {
                    $io->writeError("Failed to install FeatureContext.php file to $bootstrapPath");
                }
            } else {
                $io->write("Skipped FeatureContext.php file as it already exists and override is set to false.");
            }
        } else {
            $io->writeError("Source file not found: FeatureContext.php");
        }
    }

    private static function createDirectory($io, $path)
    {
        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            $io->writeError("Failed to create directory: $path");
        }
    }
}
