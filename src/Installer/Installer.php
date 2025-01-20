<?php

declare(strict_types=1);

namespace Salsadigitalauorg\ScaffoldTesting\Installer;

use Composer\Installer\LibraryInstaller;
use Composer\Script\Event;
use Composer\IO\IOInterface;
use Composer\Composer;

/**
 * Handles installation of Behat test files and context setup.
 */
class Installer extends LibraryInstaller
{
    protected $io;

    public function __construct(IOInterface $io, Composer $composer, string $type = 'library')
    {
        parent::__construct($io, $composer, $type);
        $this->io = $io;
    }

    /**
     * Post install/update command hook.
     */
    public static function features(Event $event): void
    {
        $io = $event->getIO();
        $io->write('[scaffold-testing] Installer::features method called');

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
                    $io->write("[scaffold-testing] Skipped $file file as it already exists and override is set to false.");
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
                    $io->write("[scaffold-testing] Installed FeatureContext.php file to $bootstrapPath");
                } else {
                    $io->writeError("[scaffold-testing] Failed to install FeatureContext.php file to $bootstrapPath");
                }
            } else {
                $io->write("[scaffold-testing] Skipped FeatureContext.php file as it already exists and override is set to false.");
            }
        } else {
            $io->writeError("[scaffold-testing] Source file not found: FeatureContext.php");
        }

        if (self::shouldUpdateFeatureContext($config)) {
            self::updateFeatureContext($targetPath, $io);
        }
    }

    /**
     * Creates necessary directories for test files.
     */
    private static function createDirectory(IOInterface $io, string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            $io->writeError("Failed to create directory: $path");
        }
    }

    /**
     * Updates or creates the FeatureContext file.
     */
    private static function updateFeatureContext(string $projectRoot, IOInterface $io): void
    {
        $featureContextPath = $projectRoot . '/tests/behat/features/bootstrap/FeatureContext.php';
        $useStatements = [
            'use Behat\Behat\Context\Context;',
            'use Behat\Behat\Hook\Scope\AfterScenarioScope;',
            'use Behat\Behat\Hook\Scope\BeforeScenarioScope;',
            'use Behat\Gherkin\Node\PyStringNode;',
            'use Behat\Gherkin\Node\TableNode;',
            'use PHPUnit\Framework\Assert;',
            'use Symfony\Component\Process\Process;',
            'use Salsadigitalauorg\ScaffoldTesting\Traits\ScaffoldTestingTrait;'
        ];

        if (!file_exists($featureContextPath)) {
            $template = self::getFeatureContextTemplate();
            file_put_contents($featureContextPath, $template);
            return;
        }

        $content = file_get_contents($featureContextPath);
        
        // Add use statements if not present
        foreach ($useStatements as $useStatement) {
            if (strpos($content, $useStatement) === false) {
                $content = preg_replace(
                    '/(namespace .*?;[\n\r]+)/s',
                    "$1\n" . $useStatement . "\n",
                    $content
                );
            }
        }

        // Add trait implementation if not present
        if (strpos($content, 'use ScaffoldTestingTrait;') === false) {
            $content = preg_replace(
                '/(class FeatureContext implements Context[\n\r]*{)/s',
                "$1\n    use ScaffoldTestingTrait;\n",
                $content
            );
        }

        file_put_contents($featureContextPath, $content);
    }

    /**
     * Gets the template for a new FeatureContext file.
     */
    private static function getFeatureContextTemplate(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace Salsadigitalauorg\ScaffoldTesting\Tests\Behat;

use Behat\Behat\Context\Context;
use Salsadigitalauorg\ScaffoldTesting\Traits\ScaffoldTestingTrait;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    use ScaffoldTestingTrait;

    /**
     * Initializes context.
     */
    public function __construct()
    {
        // Initialize your context here
    }
}
PHP;
    }

    private static function shouldUpdateFeatureContext(array $config): bool
    {
        return isset($config['override_feature_context']) && $config['override_feature_context'] === true;
    }
}
