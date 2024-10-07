#### Overview

The **Scaffold Testing Library** provides a set of default Behat tests tailored for Drupal projects, aiming to ensure consistent testing across different deployments. This library helps streamline the testing process by providing ready-to-use Behat test scenarios that cover common functionalities within Drupal sites.

#### Features

- **Home Page Test**: Ensures the homepage loads successfully and contains specific keywords or phrases.
- **Permissions Test**: Checks different user roles for appropriate access rights to content in various states (published, draft, in review).
- **Workflow Test**: Tests the expected moderation states and transitions between them.
- **Search Functionality Test**: Verifies that search indexing works and returns expected results.

#### Installation

1. **Install the Library**: Run the following command to install the library:

    ```bash
    composer require --dev salsadigitalauorg/scaffold-testing
    ```

#### Usage

After installation, the Behat test files are placed in the `tests/behat/features/` directory of your Drupal project. You can run these tests using Behat with a command similar to:

```bash
vendor/bin/behat
```

Make sure you have configured Behat properly in your Drupal project to recognize and execute these tests.

#### Configuration

To ensure the `salsadigitalauorg/scaffold-testing` runs as you execute `composer install` or `composer update`, 
add the following to the `post-install-cmd` and `post-update-cmd` sections of your project' composer.json:

```json
"scripts": {
   "install-features": "Salsadigitalauorg\\ScaffoldTesting\\Installer\\Installer::features",
   "post-install-cmd": [
        "Salsadigitalauorg\\ScaffoldTesting\\Installer\\Installer::features"
    ],
   "post-update-cmd": [
        "Salsadigitalauorg\\ScaffoldTesting\\Installer\\Installer::features"
    ]
}
```

Alternatively, add `install-features` command only:

```json
"scripts": {
   "install-features": "Salsadigitalauorg\\ScaffoldTesting\\Installer\\Installer::features",
}
```

To customize the tests or the installation path, you can modify the `extra` section in your project's `composer.json`:

```json
"extra": {
  "scaffold-testing": {
    "target-dir": "tests/behat/",
    "files": {
        "homepage.feature": false,
        "login.feature": false,
        "search.feature": true
    },
    "override_feature_context": false
  }
}
```

- `target-dir`: Specifies the base directory for test files (default: "tests/behat/").
- `files`: An object where keys are feature file names and values are boolean flags indicating whether to override existing files. When `files` key is omited, the existing files will be left untouched and missing files will be installed.
- `override_feature_context`: Whether to overwrite the existing FeatureContext.php file (default: false).

If the `files` section is omitted, all available feature files will be installed only if they don't already exist in the target directory.

The installer will create the necessary subdirectories within the `target-dir`:

#### Contributing

Contributions to the **Scaffold Testing Library** are welcome! Please feel free to submit pull requests or create issues for any bugs you discover or enhancements you suggest.

#### License

This library is provided under the MIT License. See the LICENSE file for more information.
