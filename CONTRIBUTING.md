# Contributing to 20i Repository

Thank you for considering contributing to this repository! Contributions are welcome and greatly appreciated. By following these guidelines, you can help maintain a collaborative and efficient workflow.

---

## How to Contribute

### 1. Reporting Issues

If you encounter a bug, have a feature request, or want to suggest an improvement, please create an issue.

- Go to the [Issues](https://github.com/softwarewrap/20i/issues) section of this repository.
- Use the provided issue template and include as much detail as possible, such as:
  - Steps to reproduce the issue
  - Expected and actual behavior
  - Environment details (e.g., PHP version, OS)

### 2. Suggesting Features

If you have an idea for a new feature or improvement:
- Open an issue and label it as a `Feature Request`.
- Provide a clear and concise description of the feature, along with potential use cases.

### 3. Submitting Pull Requests

If you’d like to submit code changes or add new functionality, follow these steps:

1. **Fork the Repository**:
   - Click the `Fork` button on the top-right corner of the repository page.

2. **Clone Your Fork**:
   ```bash
   git clone https://github.com/your-username/20i.git
   cd 20i
   ```

3. **Create a New Branch**:
   Use a descriptive name for your branch:
   ```bash
   git checkout -b feature/my-new-feature
   ```

4. **Make Your Changes**:
   - Add or modify files in the appropriate directories.
   - Write clear, concise, and well-documented code.
   - Ensure your code adheres to the repository’s coding standards.

5. **Run Tests**:
   - Test your changes to ensure they work as intended and do not break existing functionality.
   ```bash
   php tests/email/test-create-forward.php
   ```

6. **Commit Your Changes**:
   - Use meaningful commit messages:
     ```bash
     git commit -m "Add feature to create bulk email forwards"
     ```

7. **Push to Your Fork**:
   ```bash
   git push origin feature/my-new-feature
   ```

8. **Submit a Pull Request**:
   - Go to the original repository on GitHub.
   - Click `Pull Requests` and then `New Pull Request`.
   - Select your fork and branch as the source.
   - Provide a detailed description of your changes.

---

## Code Guidelines

To maintain consistency and readability, please follow these coding guidelines:

1. **PHP Standards**:
   - Adhere to [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.

2. **Documentation**:
   - Add comments to explain complex logic.
   - Update relevant `README.md` files for any new scripts or significant changes.

3. **File Organization**:
   - Place scripts, tests, and configs in the appropriate directories (e.g., `scripts/`, `tests/`, `configs/`).

4. **Testing**:
   - Write test scripts for new features in the `tests/` directory.
   - Ensure all tests pass before submitting your pull request.

---

## Communication

- For general questions or discussions, open an issue or reach out via the repository’s discussion forum (if enabled).
- Be respectful and professional in all communications.

---

## License

By contributing, you agree that your contributions will be licensed under the same license as this repository (GPLv3). For details, see the `LICENSE` file.

---

Thank you for your contributions and for helping improve this repository! 🚀
