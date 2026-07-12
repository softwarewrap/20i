#!/usr/bin/env php
<?php
/**
 * Determine whether a domain name is attached to any 20i hosting package.
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
 * Created: July 11, 2026
 */

declare(strict_types=1);

require_once __DIR__ . '/../../lib/bootstrap.php';
require_once __DIR__ . '/../../lib/package.php';

use function SoftwareWrap\TwentyI\getPackageByDomain;
use function SoftwareWrap\TwentyI\isValidDomain;
use function SoftwareWrap\TwentyI\normalizeDomain;

const EXIT_DOMAIN_EXISTS = 0;
const EXIT_DOMAIN_NOT_FOUND = 1;
const EXIT_OPERATIONAL_ERROR = 2;

/**
 * Display usage information.
 */
function usage(int $exitCode = EXIT_DOMAIN_EXISTS): void
{
    $script = basename($_SERVER['argv'][0]);
    $stream = $exitCode === EXIT_DOMAIN_EXISTS ? STDOUT : STDERR;

    fwrite($stream, <<<EOT
Usage:
  {$script} [--verbose] <domain>

Options:
  --verbose, -v  Display the lookup result and matching package details.
  --help, -h     Display this help text.

Exit status:
  0  The domain is attached to a package.
  1  The domain is not attached to any package.
  2  The command could not complete the lookup.

By default, the command writes no output for an ordinary found/not-found
result. Use the exit status from a shell script, or pass --verbose for
human-readable output.

Examples:
  {$script} example.com
  {$script} --verbose example.com
  if {$script} example.com; then echo "attached"; fi

EOT
    );

    exit($exitCode);
}

/**
 * Write an operational error and terminate.
 */
function fail(string $message): void
{
    fwrite(STDERR, "Error: {$message}\n");
    exit(EXIT_OPERATIONAL_ERROR);
}

$verbose = false;
$arguments = [];

for ($index = 1; $index < $argc; $index++) {
    $argument = $argv[$index];

    if ($argument === '--help' || $argument === '-h') {
        usage(EXIT_DOMAIN_EXISTS);
    }

    if ($argument === '--verbose' || $argument === '-v') {
        $verbose = true;
        continue;
    }

    if (strpos($argument, '-') === 0) {
        fail("Unknown option '{$argument}'.");
    }

    $arguments[] = $argument;
}

if (count($arguments) !== 1) {
    usage(EXIT_OPERATIONAL_ERROR);
}

$domain = normalizeDomain($arguments[0]);

if (!isValidDomain($domain)) {
    fail("Invalid domain '{$domain}'.");
}

try {
    $servicesApi = new \TwentyI\API\Services($api_key);
    $package = getPackageByDomain($servicesApi, $domain);

    if ($package === null) {
        if ($verbose) {
            echo "Domain: {$domain}\n";
            echo "Status: not attached\n";
        }

        exit(EXIT_DOMAIN_NOT_FOUND);
    }

    if ($verbose) {
        echo "Domain: {$domain}\n";
        echo "Status: attached\n";

        if (isset($package['id']) && (is_int($package['id']) || is_string($package['id']))) {
            echo 'Package ID: ' . (string) $package['id'] . "\n";
        }

        if (isset($package['names']) && is_array($package['names'])) {
            $names = [];

            foreach ($package['names'] as $name) {
                if (is_string($name)) {
                    $names[] = $name;
                }
            }

            if ($names !== []) {
                echo "Package names:\n";

                foreach ($names as $name) {
                    echo "  {$name}\n";
                }
            }
        }
    }

    exit(EXIT_DOMAIN_EXISTS);
} catch (Throwable $exception) {
    fail($exception->getMessage());
}
