# Reusable PHP Libraries

The `lib/` directory contains shared code used by the command-line tools under `scripts/`. Libraries should hold reusable validation, API discovery, DNS, configuration, and CLI behavior so that executable scripts can remain comparatively thin.

## Library Overview

| File | Purpose |
|---|---|
| `bootstrap.php` | Loads configuration and the 20i PHP API classes required by command-line scripts. |
| `cli.php` | Defines shared CLI exit codes and helpers for errors, prompts, and standard-input processing. |
| `config.php` | Loads the repository `.env` file and exposes the configured 20i API key. |
| `dns.php` | Provides TXT validation, 20i payload construction, authoritative DNS querying, DNS packet parsing, and StackDNS helpers. |
| `env.php` | Implements the lightweight `.env` parser used by `config.php`. |
| `package.php` | Provides package discovery, domain lookup, response normalization, and package metadata helpers. |

The directory also contains `20i-api-modules/`, which is the 20i PHP API library included as a Git submodule.

## `bootstrap.php`

`bootstrap.php` is the normal entry point for scripts that need authenticated access to the 20i API.

It:

- loads `env.php` and `config.php`;
- obtains the API key through `config.php`;
- loads the required 20i API exception, authentication, REST, control-panel, and services classes from `lib/20i-api-modules/`.

A CLI script typically begins with:

```php
require_once __DIR__ . '/../../lib/bootstrap.php';
```

Scripts then create the required API client, commonly:

```php
$servicesApi = new \TwentyI\API\Services($api_key);
```

## `cli.php`

Namespace:

```php
SoftwareWrap\TwentyI\Cli
```

### Exit codes

| Constant | Value | Meaning |
|---|---:|---|
| `EXIT_SUCCESS` | `0` | The operation completed successfully. |
| `EXIT_ERROR` | `1` | A usage, validation, configuration, or other fatal error occurred. |
| `EXIT_CANCELLED` | `2` | The operator declined a confirmation prompt. |
| `EXIT_PARTIAL_FAILURE` | `3` | A batch completed, but one or more targets failed. |

### Public helpers

#### `fail(string $message, int $exitCode = EXIT_ERROR): void`

Writes a formatted error message to standard error and terminates with the requested exit code.

#### `readLinesFromStdin(): array`

Reads nonempty lines from standard input and returns them as an ordered array. Batch-oriented scripts use this helper when no positional domain is supplied.

#### `confirm(string $prompt): bool`

Displays an interactive prompt and returns `true` only when the operator gives an affirmative response.

## `config.php`

`config.php` loads the repository-root `.env` file and reads:

```dotenv
API_KEY=your-20i-api-key
```

The configured value is exposed to scripts as:

```php
$api_key
```

If `API_KEY` is absent or empty, configuration loading throws a `RuntimeException` rather than allowing an unauthenticated API call to proceed.

## `dns.php`

Namespace:

```php
SoftwareWrap\TwentyI\Dns
```

`dns.php` supplies both record-oriented utilities and a pure-PHP authoritative TXT resolver.

### Record validation and construction

The public record helpers include:

- `normalizeRecordType()`
- `isSupportedRecordType()`
- `requireSupportedRecordType()`
- `normalizeRecordName()`
- `isValidRecordName()`
- `requireValidRecordName()`
- `normalizeTxtValue()`
- `requireValidTxtValue()`
- `txtValuesEqual()`
- `buildTxtRecord()`
- `buildAddTxtRecordPayload()`
- `buildRecordFqdn()`
- `containsTxtValue()`

The current write implementation supports TXT records only. `buildAddTxtRecordPayload()` creates the additive payload expected by the 20i package DNS endpoint.

### Authoritative TXT querying

The primary querying helpers are:

- `queryAuthoritativeTxtRecords()`
- `getStackDnsTxtRecords()`
- `stackDnsTxtRecordExists()`

By default, StackDNS queries are sent directly to:

```text
ns1.stackdns.com
ns2.stackdns.com
ns3.stackdns.com
ns4.stackdns.com
```

Queries disable recursion and require an authoritative answer. The implementation sends DNS packets over UDP and automatically falls back to TCP when a response is truncated.

### DNS protocol implementation

The lower-level functions implement:

- DNS query-packet construction;
- DNS-name encoding;
- UDP and TCP transport;
- exact-length socket reads;
- response-header parsing and validation;
- compressed DNS-name traversal;
- TXT resource-record parsing;
- packet-boundary validation.

This design deliberately avoids external utilities such as `dig`, Python dependencies such as `dnspython`, and recursive resolution through `dns_get_record()`.

### Architectural role

The DNS library provides the read and validation path. CLI scripts remain responsible for:

- interpreting user-facing owner-name forms such as `@`;
- resolving a target domain to a 20i package;
- submitting additive changes through the 20i API;
- reporting per-domain progress;
- deciding how to handle delayed authoritative publication.

## `env.php`

`env.php` defines the global function:

```php
load_env(string $filename): void
```

The parser:

- returns without error when the requested file does not exist;
- ignores blank lines;
- ignores lines beginning with `#`;
- splits each remaining line on the first `=` character;
- trims keys and values;
- places the result in `$_ENV`.

It is intentionally small and does not attempt to implement every feature supported by full dotenv libraries.

## `package.php`

Namespace:

```php
SoftwareWrap\TwentyI
```

### Domain helpers

- `normalizeDomain()` normalizes domain input for comparison and lookup.
- `isValidDomain()` validates a normalized domain.

### API-response handling

- `responseToArray()` converts supported API response values into arrays suitable for the rest of the toolkit.

### Package discovery and lookup

- `getPackages()` retrieves the packages visible to the authenticated account.
- `findPackageByDomain()` searches an existing package collection for a domain.
- `getPackageByDomain()` retrieves packages and resolves a domain in one operation.

### Package metadata

- `getPackageId()` returns the package identifier used by 20i API endpoints.
- `getPackageSelector()` returns the package selector used by package-oriented operations.
- `getPackageDomains()` returns the normalized domains attached to a package.

## Design Guidance

When adding new functionality:

- place reusable business logic and protocol handling in `lib/`;
- keep direct terminal output and argument parsing in `scripts/`;
- throw exceptions from libraries when callers need to decide how to report or aggregate failures;
- normalize values at clear boundaries and preserve a canonical internal representation;
- avoid coupling a general-purpose library function to one specific CLI command;
- prefer deterministic return values over hidden side effects.
