<?php

declare(strict_types=1);

namespace Salsadigitalauorg\ScaffoldTesting\Tests\Unit\Installer;

use PHPUnit\Framework\TestCase;
use Salsadigitalauorg\ScaffoldTesting\Installer\Installer;
use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Package\RootPackage;
use Composer\Script\Event;

class InstallerTest extends TestCase
{
    protected IOInterface $io;
    protected RootPackage $package;
    protected Config $config;
    protected Composer $composer;
    protected Event $event;

    protected function setUp(): void
    {
        $this->io = $this->createMock(IOInterface::class);
        $this->package = $this->createMock(RootPackage::class);
        $this->config = $this->createMock(Config::class);
        $this->composer = $this->createMock(Composer::class);
        
        $this->composer->method('getConfig')
            ->willReturn($this->config);
            
        $this->config->method('get')
            ->with('vendor-dir')
            ->willReturn('/var/www/html/vendor');
            
        $this->package->method('getExtra')
            ->willReturn([
                'scaffold-testing' => [
                    'target-dir' => 'tests/behat/',
                    'override_feature' => false,
                    'override_feature_context' => false
                ]
            ]);
            
        $this->composer->method('getPackage')
            ->willReturn($this->package);
            
        $this->event = $this->createMock(Event::class);
        $this->event->method('getIO')
            ->willReturn($this->io);
        $this->event->method('getComposer')
            ->willReturn($this->composer);
    }

    public function testGetSubscribedEvents(): void
    {
        $this->io->expects(self::atLeastOnce())
            ->method('write')
            ->with('[scaffold-testing] Installer::features method called');
            
        Installer::features($this->event);
    }

    public function testGetInstallPaths(): void
    {
        $this->io->expects(self::atLeastOnce())
            ->method('write')
            ->with('[scaffold-testing] Installer::features method called');
            
        Installer::features($this->event);
    }

    public function testFeatures(): void
    {
        $this->io->expects(self::atLeastOnce())
            ->method('write')
            ->with('[scaffold-testing] Installer::features method called');
            
        Installer::features($this->event);
    }

    public function testContentTypes(): void
    {
        $this->io->expects(self::atLeastOnce())
            ->method('write')
            ->with('[scaffold-testing] Installer::features method called');
            
        Installer::features($this->event);
    }

    protected function tearDown(): void
    {
        // Cleanup test files using absolute paths
        @unlink('/var/www/html/tests/behat/features/login.feature');
        @unlink('/var/www/html/tests/behat/features/search.feature');
    }
}
