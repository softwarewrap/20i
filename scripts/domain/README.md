# `scripts/domain`

## Overview

The `scripts/domain` directory contains command-line tools for managing
domains within a 20i reseller account.

## Current Commands

### `attach-domain-to-package.php`

Attaches one or more domains to an existing hosting package.

**Features**

-   Package discovery
-   Batch processing
-   `--dry-run`
-   `--yes`
-   `--skip`
-   Deterministic progress reporting
-   Duplicate detection

## Common CLI Conventions

-   Validate before mutation.
-   Keep business logic in `lib/`.
-   Support batch operations.
-   Summarize results.
