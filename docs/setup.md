# Setting Up Your Environment

This guide helps new resellers configure their development environment to work with the scripts and tools in this repository.

---

## Prerequisites

Before you begin, ensure you have the following installed and configured on your system:

1. **PHP**
   - Version: 7.4 or later
   - Install via your operating system's package manager. For example:
     ```bash
     sudo apt install php
     # Or for RHEL-based systems:
     sudo dnf install php
     ```

2. **Composer**
   - A dependency manager for PHP.
   - Install Composer globally:
     ```bash
     curl -sS https://getcomposer.org/installer | php
     sudo mv composer.phar /usr/local/bin/composer
     ```

3. **Git**
   - Required for cloning this repository.
   - Install Git:
     ```bash
     sudo apt install git
     # Or for RHEL-based systems:
     sudo dnf install git
     ```

4. **20i API Key**
   - Obtain your API key from the 20i reseller control panel.
   - Save it as an environment variable:
     ```bash
     export GENERAL_API_KEY="your-20i-api-key"
     ```

---

## Clone the Repository

Clone this repository to your local machine:
```bash
git clone https://github.com/softwarewrap/20i.git
cd 20i
```

---

## Install Dependencies

1. Navigate to the repository's root directory:
   ```bash
   cd 20i
   ```

2. Install required PHP libraries using Composer:
   ```bash
   composer install
   ```

---

## Configuration

### Option 1: Use Environment Variables

Set the required environment variables for your session:
```bash
export GENERAL_API_KEY="your-20i-api-key"
```
To make this change permanent, add it to your shell configuration file (e.g., `.bashrc`, `.zshrc`):
```bash
echo 'export GENERAL_API_KEY="your-20i-api-key"' >> ~/.bashrc
source ~/.bashrc
```

### Option 2: Use a Configuration File

Create a JSON configuration file (`config/config.json`) with your API key:
```json
{
  "api_key": "your-20i-api-key"
}
```

Update scripts to read from this file if not already configured.

---

## Testing the Setup

Run a test script to confirm everything is working correctly:

Example:
```bash
php scripts/email/create-forward.php test@example.com forward@example.com
```
If no errors occur, your environment is ready.

---

## Troubleshooting

1. **PHP Missing Extensions**
   - Ensure all required PHP extensions are installed:
     ```bash
     sudo apt install php-json php-curl php-mbstring
     ```

2. **Invalid API Key**
   - Double-check the API key you obtained from the 20i reseller control panel.

3. **Permission Issues**
   - Ensure the scripts have execute permissions:
     ```bash
     chmod +x scripts/email/create-forward.php
     ```

---

With these steps completed, you are ready to start using the tools and scripts in this repository. For further assistance, refer to the `docs/api-guide.md` or raise an issue in the repository.

