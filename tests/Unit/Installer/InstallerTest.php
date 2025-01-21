<?php

declare(strict_types=1);

namespace Salsadigitalauorg\ScaffoldTesting\Tests\Unit\Installer;

use PHPUnit\Framework\TestCase;
use Salsadigitalauorg\ScaffoldTesting\Installer\Installer;

class InstallerTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Installer::class));
    }
}
