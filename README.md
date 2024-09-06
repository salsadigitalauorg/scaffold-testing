#### Overview

The **Scaffold Testing Library** provides a set of default Behat tests tailored for Drupal projects, aiming to ensure consistent testing across different deployments. This library helps streamline the testing process by providing ready-to-use Behat test scenarios that cover common functionalities within Drupal sites.

#### Features

- **Home Page Test**: Ensures the homepage loads successfully and contains specific keywords or phrases.
- **Permissions Test**: Checks different user roles for appropriate access rights to content in various states (published, draft, in review).
- **Workflow Test**: Tests the expected moderation states and transitions between them.
- **Search Functionality Test**: Verifies that search indexing works and returns expected results.

#### Installation

1. **Add the Library to Your Project**: You can include this library in your Drupal project by adding it to your `composer.json` file. Ensure you have access to the library path if it's hosted locally or available on a VCS. For local development, you can use:

    ```json
    {
      "repositories": [
        {
          "type": "path",
          "url": "/path/to/scaffold-testing",
          "options": {
            "symlink": false
          }
        }
      ],
      "require-dev": {
        "salsadigitalauorg/scaffold-testing": "*"
      }
    }
    ```

2. **Install the Library**: Run the following command to install the library:

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

To customize the tests or the installation path, you can modify the `extra` section in your project's `composer.json`:

```json
"extra": {
  "scaffold-testing": {
    "target-dir": "custom/path/for/features/",
    "files": [
      "customized_home.feature",
      "customized_permissions.feature"
    ],
    "override": "true"
  }
}
```

#### Contributing

Contributions to the **Scaffold Testing Library** are welcome! Please feel free to submit pull requests or create issues for any bugs you discover or enhancements you suggest.

#### License

This library is provided under the MIT License. See the LICENSE file for more information.
