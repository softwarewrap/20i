#!/usr/bin/env php
<?php
/**
 * Plan the addition of one TXT DNS record to one or more domains.
 *
 * This initial implementation provides the complete command-line interface,
 * validation, domain selection, package resolution, --all behavior, and
 * dry-run reporting. DNS retrieval and mutation will be enabled once the
 * supported 20i DNS endpoint for externally registered domains is confirmed.
 *
 * This file is part of a software project licensed under the
 * GNU General Public License v3.0.
 *
 * Copyright (C) 2026 Stephen Amerige
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * Original Author: Stephen Amerige, Raleigh, North Carolina
 * Created: July 12, 2026
 */

declare(strict_types=1);

require_once __DIR__ . '/../../lib/bootstrap.php';
require_once __DIR__ . '/../../lib/cli.php';
require_once __DIR__ . '/../../lib/dns.php';
require_once __DIR__ . '/../../lib/package.php';

use function SoftwareWrap\TwentyI\Cli\fail;
use function SoftwareWrap\TwentyI\Cli\readLinesFromStdin;
use function SoftwareWrap\TwentyI\Dns\buildAddTxtRecordPayload;
use function SoftwareWrap\TwentyI\Dns\requireSupportedRecordType;
use function SoftwareWrap\TwentyI\Dns\requireValidRecordName;
use function SoftwareWrap\TwentyI\Dns\requireValidTxtValue;
use function SoftwareWrap\TwentyI\findPackageByDomain;
use function SoftwareWrap\TwentyI\getPackageDomains;
use function SoftwareWrap\TwentyI\getPackageId;
use function SoftwareWrap\TwentyI\getPackageSelector;
use function SoftwareWrap\TwentyI\getPackages;
use function SoftwareWrap\TwentyI\isValidDomain;
use function SoftwareWrap\TwentyI\normalizeDomain;

use const SoftwareWrap\TwentyI\Cli\EXIT_ERROR;
use const SoftwareWrap\TwentyI\Cli\EXIT_PARTIAL_FAILURE;
use const SoftwareWrap\TwentyI\Cli\EXIT_SUCCESS;

/**
 * Display usage information.
 */
function usage(int $exitCode = EXIT_SUCCESS): void
{
    $script = basename($_SERVER['argv'][0]);
    $stream = $exitCode === EXIT_SUCCESS ? STDOUT : STDERR;

    fwrite($stream, <<<EOT
Usage:
  {$script} [--dry-run] [--yes] [--skip] <domain>
      --name <dns-name> --type TXT --value <string>

  {$script} [--dry-run] [--yes] [--skip]
      --name <dns-name> --type TXT --value <string> < domains.txt

  {$script} [--dry-run] [--yes] [--skip] --all <package-domain>
      --name <dns-name> --type TXT --value <string>

Options:
  --name <dns-name>
             DNS owner name. Use @ for the zone apex.
  --type TXT
             DNS record type. The initial implementation supports TXT only.
  --value <string>
             TXT record value.
  --all      Apply the record to every domain attached to the package
             identified by the positional <package-domain>.
  --dry-run  Resolve and validate the request without changing DNS.
  --yes, -y  Skip any future batch confirmation prompt.
  --skip     When DNS record inspection is enabled, skip domains on which
             the identical TXT record already exists.
  --help, -h Display this help text.

Examples:
  {$script} example.com \
      --name @ \
      --type TXT \
      --value "This domain is for sale"

  {$script} \
      --name @ \
      --type TXT \
      --value "This domain is for sale" \
      < domains.txt

  {$script} --all lowpricereseller.com \
      --name @ \
      --type TXT \
      --value "This domain is for sale"

A single positional domain processes one domain. With no positional domain,
domains are read from standard input. The --all option requires one positional
domain that identifies a package.

This version supports planning and dry-run validation only. DNS changes remain
disabled until the supported 20i DNS endpoint is confirmed.

EOT
    );

    exit($exitCode);
}

/**
 * Return the value following an option or terminate with a useful error.
 */
function requireOptionValue(
    string $option,
    int &$index,
    int $argc,
    array $argv
): string {
    $index++;

    if ($index >= $argc) {
        fail("Option '{$option}' requires a value.");
    }

    return $argv[$index];
}

/**
 * Read and normalize domains from standard input.
 *
 * @return array<int,string>
 */
function readDomainsFromStdin(): array
{
    return array_map(
        'SoftwareWrap\\TwentyI\\normalizeDomain',
        readLinesFromStdin()
    );
}

/**
 * Validate and deduplicate domains while preserving input order.
 *
 * @param array<int,string> $domains
 * @return array<int,string>
 */
function validateDomains(array $domains): array
{
    $unique = [];

    foreach ($domains as $domain) {
        $domain = normalizeDomain($domain);

        if (!isValidDomain($domain)) {
            fail("Invalid domain '{$domain}'.");
        }

        $unique[$domain] = true;
    }

    return array_keys($unique);
}

/*
 * Parse options and positional arguments.
 */
$dryRun = false;
$assumeYes = false;
$skipExisting = false;
$allDomains = false;

$recordName = null;
$recordType = null;
$recordValue = null;
$arguments = [];

for ($index = 1; $index < $argc; $index++) {
    $argument = $argv[$index];

    if ($argument === '--help' || $argument === '-h') {
        usage(EXIT_SUCCESS);
    }

    if ($argument === '--dry-run') {
        $dryRun = true;
        continue;
    }

    if ($argument === '--yes' || $argument === '-y') {
        $assumeYes = true;
        continue;
    }

    if ($argument === '--skip') {
        $skipExisting = true;
        continue;
    }

    if ($argument === '--all') {
        $allDomains = true;
        continue;
    }

    if ($argument === '--name') {
        $recordName = requireOptionValue(
            '--name',
            $index,
            $argc,
            $argv
        );
        continue;
    }

    if ($argument === '--type') {
        $recordType = requireOptionValue(
            '--type',
            $index,
            $argc,
            $argv
        );
        continue;
    }

    if ($argument === '--value') {
        $recordValue = requireOptionValue(
            '--value',
            $index,
            $argc,
            $argv
        );
        continue;
    }

    if (strpos($argument, '-') === 0) {
        fail("Unknown option '{$argument}'.");
    }

    $arguments[] = $argument;
}

if ($recordName === null) {
    fail('The --name option is required.');
}

if ($recordType === null) {
    fail('The --type option is required.');
}

if ($recordValue === null) {
    fail('The --value option is required.');
}

try {
    $recordName = requireValidRecordName($recordName);
    $recordType = requireSupportedRecordType($recordType);
    $recordValue = requireValidTxtValue($recordValue);

    /*
     * Construct the payload now so validation covers the exact structure
     * that the eventual API mutation will use.
     */
    $payload = buildAddTxtRecordPayload(
        $recordName,
        $recordValue
    );
} catch (Throwable $exception) {
    fail($exception->getMessage());
}

if ($allDomains) {
    if (count($arguments) !== 1) {
        fail(
            'The --all option requires exactly one positional package domain.'
        );
    }

    $selectorDomain = normalizeDomain($arguments[0]);

    if (!isValidDomain($selectorDomain)) {
        fail("Invalid package domain '{$selectorDomain}'.");
    }

    $requestedDomains = [];
} elseif (count($arguments) === 1) {
    $selectorDomain = null;
    $requestedDomains = validateDomains([
        normalizeDomain($arguments[0]),
    ]);
} elseif (count($arguments) === 0) {
    $selectorDomain = null;
    $requestedDomains = validateDomains(
        readDomainsFromStdin()
    );

    if ($requestedDomains === []) {
        fail('No domains were provided.');
    }
} else {
    usage(EXIT_ERROR);
}

try {
    $servicesApi = new \TwentyI\API\Services($api_key);
    $packages = getPackages($servicesApi);

    $targets = [];

    if ($allDomains) {
        $package = findPackageByDomain(
            $packages,
            $selectorDomain
        );

        if ($package === null) {
            fail("No package contains '{$selectorDomain}'.");
        }

        $packageId = getPackageId($package);
        $packageSelector = getPackageSelector($package);
        $packageDomains = getPackageDomains($package);

        if ($packageDomains === []) {
            fail(
                "Package '{$packageId}' does not contain any usable domains."
            );
        }

        foreach ($packageDomains as $domain) {
            $targets[] = [
                'domain' => $domain,
                'packageId' => $packageId,
                'packageSelector' => $packageSelector,
            ];
        }
    } else {
        foreach ($requestedDomains as $domain) {
            $package = findPackageByDomain(
                $packages,
                $domain
            );

            if ($package === null) {
                $targets[] = [
                    'domain' => $domain,
                    'packageId' => null,
                    'packageSelector' => null,
                ];

                continue;
            }

            $targets[] = [
                'domain' => $domain,
                'packageId' => getPackageId($package),
                'packageSelector' => getPackageSelector($package),
            ];
        }
    }

    echo "DNS record:\n";
    echo "  Name:  {$recordName}\n";
    echo "  Type:  {$recordType}\n";
    echo "  Value: {$recordValue}\n";
    echo "\nRequested mode: "
        . ($allDomains ? 'all domains in package' : 'explicit domains')
        . "\n";
    echo "Skip identical records: "
        . ($skipExisting ? 'yes' : 'no')
        . "\n";
    echo "Confirmation bypass: "
        . ($assumeYes ? 'yes' : 'no')
        . "\n";
    echo "Domains resolved: " . count($targets) . "\n";

    $resolvableCount = 0;
    $unresolvedCount = 0;

    echo "\nPlanning results:\n";

    foreach ($targets as $offset => $target) {
        $position = $offset + 1;
        $total = count($targets);
        $domain = $target['domain'];

        if ($target['packageId'] === null) {
            echo "[{$position}/{$total}] {$domain} ... "
                . "ERROR: not attached to any visible package\n";
            $unresolvedCount++;
            continue;
        }

        echo "[{$position}/{$total}] {$domain} ... "
            . "WOULD ADD {$recordType} {$recordName} "
            . "to package {$target['packageId']}"
            . " ({$target['packageSelector']})\n";

        $resolvableCount++;
    }

    echo "\nPlanning complete.\n";
    echo "  Eligible domains: {$resolvableCount}\n";
    echo "  Unresolved domains: {$unresolvedCount}\n";

    if (!$dryRun) {
        fwrite(
            STDERR,
            "\nError: DNS mutation is not yet enabled because the supported "
            . "20i DNS endpoint for externally registered domains remains "
            . "unconfirmed. No DNS changes were made.\n"
        );

        exit(EXIT_ERROR);
    }

    echo "\nDry run complete. No DNS changes were made.\n";

    /*
     * The payload variable is deliberately retained even though it is not
     * submitted yet. It proves that the planned request has already passed
     * DNS validation and payload construction.
     */
    unset($payload);

    exit(
        $unresolvedCount === 0
            ? EXIT_SUCCESS
            : EXIT_PARTIAL_FAILURE
    );
} catch (Throwable $exception) {
    fail($exception->getMessage());
}
