# Archive Directory

This directory contains deprecated or outdated scripts, files, or content from the repository. These items are retained for historical purposes or to provide references for older implementations.

---

## Purpose

The `archive/` directory serves the following purposes:

1. **Preserve History**:
   - Retain scripts and files that are no longer actively used or maintained.
   - Provide a reference for older methods or approaches.

2. **Avoid Clutter**:
   - Move deprecated content out of active development directories to keep the repository clean and organized.

3. **Support Legacy Use**:
   - Allow users to access older versions of scripts if needed for specific legacy use cases.

---

## Guidelines for Archiving Content

1. **When to Archive**:
   - A script or file is no longer relevant or has been replaced by a better implementation.
   - A feature or API endpoint has been deprecated or removed by 20i.
   - The content is deemed unnecessary but still has potential reference value.

2. **How to Archive**:
   - Move the file or directory into `archive/`.
   - Update the `archive/README.md` with details about the archived content.
   - Add a note in the relevant active documentation (if applicable) pointing to the archived version.

3. **Documentation**:
   - For each archived item, include a brief explanation of its purpose and why it was deprecated.

---

## Archived Content

### Example Structure

```plaintext
archive/
├── old-email-scripts/
│   ├── create-forward-legacy.php
│   └── README.md
└── old-domain-scripts/
    ├── update-dns-v1.php
    └── README.md
```

### Current Archives

1. **`old-email-scripts/`**
   - **`create-forward-legacy.php`**: An earlier version of the email forwarding script. Deprecated due to API changes and replaced by `scripts/email/create-forward.php`.

2. **`old-domain-scripts/`**
   - **`update-dns-v1.php`**: Initial implementation of DNS updates. Superseded by `scripts/domain/update-dns.php`.

---

## Notes

- Content in this directory is not actively maintained.
- Use archived scripts and files at your own discretion, as they may not be compatible with the current 20i API or other dependencies.
- For active development, refer to the main `scripts/` and `docs/` directories.

---

For questions or contributions related to archived content, please raise an issue in the repository or refer to `CONTRIBUTING.md`.
