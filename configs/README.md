# Configs Directory

This directory contains configuration templates and examples to help you set up and customize the scripts in this repository.

---

## Purpose

The files in this directory serve the following purposes:
- Provide templates for environment variables or configuration files.
- Allow for easy customization of scripts to suit your specific environment.
- Centralize configuration to minimize hardcoded values in scripts.

---

## Files in This Directory

### `env-template.sh`

This file provides an example of how to set up environment variables required by the scripts.

**Template Example:**
```bash
# Replace with your actual 20i API key
export GENERAL_API_KEY="your-20i-api-key"

# Add any additional environment variables here
```

**Usage:**
1. Copy `env-template.sh` to a new file (e.g., `env.sh`).
2. Replace placeholder values with your actual credentials.
3. Source the file to load the variables into your shell:
   ```bash
   source env.sh
   ```

---

### `config-sample.json`

This file demonstrates how to use a JSON configuration file for storing credentials and settings.

**Template Example:**
```json
{
  "api_key": "your-20i-api-key",
  "default_domain": "example.com"
}
```

**Usage:**
1. Copy `config-sample.json` to a new file (e.g., `config.json`).
2. Replace placeholder values with your actual settings.
3. Update scripts to load the configuration file if not already configured.

---

## Best Practices

1. **Keep Credentials Secure**:
   - Never commit sensitive files like `env.sh` or `config.json` to version control.
   - Add these files to `.gitignore`:
     ```bash
     echo "configs/env.sh" >> .gitignore
     echo "configs/config.json" >> .gitignore
     ```

2. **Use Environment Variables for Secrets**:
   - Prefer environment variables for sensitive data like API keys.

3. **Organize Configurations**:
   - Use descriptive file names for multiple configurations if needed (e.g., `config-prod.json`, `config-dev.json`).

---

## Troubleshooting

1. **Missing Configuration**:
   - Ensure your configuration file or environment variables are correctly set.
   - Double-check paths and filenames if a script reports missing files.

2. **Permission Issues**:
   - Verify that your configuration files have appropriate permissions:
     ```bash
     chmod 600 configs/config.json
     ```

3. **Script Errors**:
   - If a script fails due to missing configurations, check the `README.md` in the relevant script directory for specific instructions.

---

For further assistance, refer to the `docs/setup.md` or raise an issue in the repository.
