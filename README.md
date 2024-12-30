# 20i

**Tagline:** Software that Helps 20i.com Resellers

Welcome to the 20i repository! This project provides scripts, tools, and resources tailored for 20i.com resellers to streamline common tasks, automate processes, and optimize the reseller experience. Whether you are just starting or looking to enhance your existing workflow, this repository offers a growing collection of utilities to help you succeed.

---

## Features

- **Email Management:** Scripts to create, list, and manage email forwards.
- **Domain Tools:** Utilities to manage domains, update DNS records, and more.
- **Extensibility:** A flexible structure that allows for future expansion into new areas.
- **Documentation:** Comprehensive guides to help you get started and maximize the tools.

---

## Getting Started

### Prerequisites

Ensure you have the following:
- PHP 7.4 or later
- Composer (PHP dependency manager)
- A 20i reseller account and API key
- Git for cloning the repository

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/softwarewrap/20i.git
   cd 20i
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up your API key:
   - Option 1: Use environment variables (preferred)
     ```bash
     export GENERAL_API_KEY="your-20i-api-key"
     ```
   - Option 2: Use a configuration file (`configs/config.json`). See `configs/README.md` for details.

---

## Repository Structure

```plaintext
20i/
├── scripts/               # Scripts for various tasks
│   ├── email/             # Email management scripts
│   ├── domain/            # Domain-related scripts
├── docs/                  # Documentation and guides
├── configs/               # Configuration templates
├── tests/                 # Test scripts
├── archive/               # Deprecated content
├── .github/               # GitHub-specific configurations
├── LICENSE                # Repository license
├── CONTRIBUTING.md        # Contribution guidelines
├── CODE_OF_CONDUCT.md     # Code of conduct for contributors
└── README.md              # Repository overview
```

---

## Example Usage

### Create an Email Forward

```bash
php scripts/email/create-forward.php alice@example.com bob@example.org
```

### List Domains

```bash
php scripts/domain/list-domains.php
```

---

## Contributing

Contributions are welcome! Please read `CONTRIBUTING.md` for details on our code of conduct, coding standards, and how to submit a pull request.

---

## License

This project is licensed under the GNU General Public License v3.0. See the `LICENSE` file for details.

---

## Contact

If you have any questions, issues, or suggestions, feel free to [open an issue](https://github.com/softwarewrap/20i/issues).

---

We hope this repository helps make your 20i reseller journey easier and more efficient. Happy reselling! 🚀
