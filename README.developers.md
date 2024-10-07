#### Overview

The **Scaffold Testing Library** provides a set of default Behat tests tailored for Drupal projects, aiming to ensure consistent testing across different deployments. This library helps streamline the testing process by providing ready-to-use Behat test scenarios that cover common functionalities within Drupal sites.

#### Installation for developers.

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
Refer to the [README.md](README.md) for more information.