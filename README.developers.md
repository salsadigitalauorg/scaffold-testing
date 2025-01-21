# Developer Documentation

## Testing

### Unit Tests

The package includes comprehensive unit tests for all configuration options. Tests are run in a Docker container via GitHub Actions.

#### Test Cases

1. Default Configuration:
   - Tests default target directory
   - Default override settings

2. Custom Target Directory:
   - Tests custom directory path
   - Verifies file creation in custom location

3. Specific Feature Files:
   - Tests selective feature file installation
   - Individual override settings per file

4. Override Features:
   - Tests global override setting
   - Tests per-file override settings

5. Feature Context:
   - Tests FeatureContext.php installation
   - Tests override behavior

### Running Tests Locally

```bash
# Run PHPUnit tests
composer phpunit-test

# Run tests in Docker (same as GHA)
act push -W .github/workflows/test.yml --container-architecture linux/amd64 -v
```

### Test Environment

Tests are executed in a Docker container with the following setup:
- PHP 8.3
- Composer 2.x
- PHPUnit 9.6

### Adding New Tests

When adding new test cases:
1. Create test method in `tests/Unit/Installer/InstallerTest.php`
2. Mock necessary Composer components
3. Add cleanup in tearDown() if creating files
4. Update test documentation

## Configuration Options

### Target Directory
- Default: `tests/behat/`
- Can be customized to any valid path
- Must be writable by the process

### Feature Files
- Located in `tests/behat/features/`
- Default files:
  - homepage.feature
  - login.feature
  - search.feature
  - contenttypes.feature

### Override Settings
- Global override: `override_feature`
- Per-file override in `files` array
- FeatureContext override: `override_feature_context`

## Development Guidelines

1. Follow PSR-12 coding standards
2. Add unit tests for new features
3. Update documentation for configuration changes
4. Test in Docker environment before committing