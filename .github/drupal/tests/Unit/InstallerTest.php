<?php

namespace Salsadigitalauorg\ScaffoldTesting\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Composer\Composer;
use Composer\Config;
use Composer\Package\RootPackage;
use Composer\Script\Event;
use Composer\IO\NullIO;
use Salsadigitalauorg\ScaffoldTesting\Installer\Installer;

class InstallerTest extends TestCase
{
    private $testDir;
    private $vendorDir;
    private $packageDir;

    protected function setUp(): void
    {
        parent::setUp();
        // Use the project root directory
        $this->testDir = getcwd();
        $this->vendorDir = $this->testDir . '/vendor/salsadigitalauorg/scaffold-testing';
        $this->packageDir = dirname(dirname(dirname(__DIR__))); // Go up to scaffold-testing root

        if (is_dir($this->testDir . '/tests')) {
            $this->removeDirectory($this->testDir . '/tests');
        }
        mkdir($this->vendorDir, 0777, true);
        chdir($this->testDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir . '/tests')) {
            $this->removeDirectory($this->testDir . '/tests');
        }
        parent::tearDown();
    }

    public function testFeatureInstallation()
    {
        // Create a mock Composer setup
        $composer = new Composer();
        $config = new Config();
        $config->merge(['config' => ['vendor-dir' => $this->testDir . '/vendor']]);
        $composer->setConfig($config);

        $package = new RootPackage('drupal/recommended-project', '1.0.0.0', '1.0.0');
        $package->setExtra([
            'scaffold-testing' => [
                'target-dir' => 'tests/behat/',
                'files' => ['homepage.feature', 'login.feature', 'search.feature'],
                'override_feature' => false,
                'override_feature_context' => false
            ]
        ]);
        $composer->setPackage($package);

        $io = new NullIO();
        $event = new Event('post-install-cmd', $composer, $io);

        // Create source files in the simulated vendor directory
        mkdir($this->vendorDir . '/features', 0777, true);
        file_put_contents($this->vendorDir . '/features/homepage.feature', 'Feature: Homepage');
        file_put_contents($this->vendorDir . '/features/login.feature', 'Feature: Login');
        file_put_contents($this->vendorDir . '/features/search.feature', 'Feature: Search');
        
        // Create bootstrap directory with FeatureContext
        mkdir($this->vendorDir . '/bootstrap', 0777, true);
        file_put_contents($this->vendorDir . '/bootstrap/FeatureContext.php', '<?php // FeatureContext class');

        // Run the installer
        Installer::features($event);

        // Assert feature files were copied
        $this->assertFileExists($this->testDir . '/tests/behat/features/homepage.feature');
        $this->assertFileExists($this->testDir . '/tests/behat/features/login.feature');
        $this->assertFileExists($this->testDir . '/tests/behat/features/search.feature');

        // Assert FeatureContext was copied (in the simple path structure)
        $this->assertFileExists($this->testDir . '/tests/behat/bootstrap/FeatureContext.php');

        // Test skipping (not overwriting)
        file_put_contents($this->testDir . '/tests/behat/features/homepage.feature', 'Modified: Homepage');
        Installer::features($event);
        $this->assertEquals('Modified: Homepage', file_get_contents($this->testDir . '/tests/behat/features/homepage.feature'));

        // Test overwriting
        $package->setExtra([
            'scaffold-testing' => [
                'target-dir' => 'tests/behat/',
                'files' => ['homepage.feature', 'login.feature', 'search.feature'],
                'override_feature' => true,
                'override_feature_context' => true
            ]
        ]);
        Installer::features($event);
        $this->assertEquals('Feature: Homepage', file_get_contents($this->testDir . '/tests/behat/features/homepage.feature'));
    }

    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        $this->removeDirectory($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }
} 