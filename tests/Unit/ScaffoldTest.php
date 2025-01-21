declare(strict_types=1);

namespace Salsadigitalauorg\ScaffoldTesting\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests the scaffold testing setup.
 */
class ScaffoldTest extends TestCase {

  /**
   * Tests that the scaffold testing structure is correct.
   */
  public function testScaffoldStructure(): void {
    $projectRoot = '/var/www/html';
    
    // Test that the behat directory exists.
    $this->assertDirectoryExists($projectRoot . '/tests/behat');
    
    // Test that the FeatureContext file exists.
    $this->assertFileExists($projectRoot . '/tests/behat/bootstrap/FeatureContext.php');
  }

} 