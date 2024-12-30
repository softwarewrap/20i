# Tests Directory

This directory contains test scripts and resources to validate the functionality of the tools and scripts in this repository.

---

## Purpose

The purpose of these tests is to:
- Ensure the scripts perform their intended functions.
- Validate integration with the 20i API.
- Catch potential issues or regressions when scripts are updated.

---

## Test Structure

### Subdirectories

- **`email/`**: Tests for email-related scripts (e.g., `create-forward.php`, `list-forwards.php`).
- **`domain/`**: Tests for domain-related scripts (e.g., `list-domains.php`, `update-dns.php`).

### Example Files

- `test-create-forward.php`: Tests the `create-forward.php` script to ensure email forwards are created correctly.
- `test-list-domains.php`: Tests the `list-domains.php` script for retrieving domains.

---

## Running Tests

### Prerequisites

1. Ensure the environment is set up and dependencies are installed as described in `docs/setup.md`.
2. Export your 20i API key:
   ```bash
   export GENERAL_API_KEY="your-20i-api-key"
   ```

### Executing a Test Script

Navigate to the `tests/` directory and run a test script using PHP:
```bash
php email/test-create-forward.php
```

### Testing Workflow Example

1. Create a test forward using the `create-forward.php` script.
2. Verify the forward exists using the `list-forwards.php` script.
3. Clean up by deleting the test forward (if applicable).

---

## Writing New Tests

When adding new tests:

1. Place the test script in the appropriate subdirectory (`email/`, `domain/`, etc.).
2. Follow these best practices:
   - Use descriptive test names.
   - Validate both expected success and failure scenarios.
   - Add comments to explain test logic.

### Template for a Basic Test Script

```php
<?php
require_once "../../vendor/autoload.php";

// Setup test variables
$testEmail = "test@example.com";
$forwardEmail = "forward@example.com";

try {
    // Call the script or function being tested
    echo "Running test for create-forward.php\n";
    $result = createForward($testEmail, $forwardEmail);

    // Check results
    if ($result) {
        echo "Test passed: Forward created successfully.\n";
    } else {
        echo "Test failed: Forward was not created.\n";
    }
} catch (Exception $e) {
    echo "Test encountered an error: " . $e->getMessage() . "\n";
}
```

---

## Troubleshooting Tests

1. **Environment Issues**:
   - Ensure your API key is correct and active.
   - Verify that your PHP environment has all required extensions installed (`php-json`, `php-curl`, etc.).

2. **Permission Errors**:
   - Make sure test scripts have execute permissions:
     ```bash
     chmod +x email/test-create-forward.php
     ```

3. **API Errors**:
   - Check the 20i API documentation for changes or rate limits.

---

For questions or contributions, refer to `CONTRIBUTING.md` or raise an issue in the repository.
