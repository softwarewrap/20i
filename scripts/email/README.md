# Email Scripts

This directory contains scripts designed to help manage email-related tasks for 20i resellers. These scripts utilize the 20i API to perform common email-related operations such as creating email forwards and listing existing forwards.

## Available Scripts

### `create-forward.php`
This script creates an email forward for a specified email address within a domain managed by your 20i account.

**Usage:**
```bash
./create-forward.php <from_email> <to_email>
```

**Example:**
```bash
./create-forward.php alice@example.com bob@example.org
```
This will forward all emails sent to `alice@example.com` to `bob@example.org`.

### `list-forwards.php`
(Placeholder script)
This script will list all existing email forwards for a specified domain.

**Usage:**
```bash
./list-forwards.php <domain>
```

**Example:**
```bash
./list-forwards.php example.com
```

## Dependencies

- PHP 7.4 or later
- 20i API key
- Required PHP libraries (installed via Composer):
  - `vendor/autoload.php` for API interaction

## Configuration

Ensure your environment is configured with the necessary API credentials. You can set up environment variables or use a configuration file:

### Using Environment Variables
```bash
export GENERAL_API_KEY="your-20i-api-key"
```

### Using a Configuration File
Place your API key and other settings in a configuration file (`config.json`) and ensure the scripts are updated to load it:

```json
{
  "api_key": "your-20i-api-key"
}
```

## Future Enhancements

1. Add the ability to delete email forwards.
2. Implement bulk operations for managing multiple forwards at once.
3. Expand error handling and logging for better debugging.

---

If you encounter any issues or have suggestions for improvement, please contribute via pull requests or create an issue in the repository.
