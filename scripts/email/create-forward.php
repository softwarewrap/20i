#!/bin/php

<?php
/**
 * This file is part of a software project and is licensed under the GNU General Public License v3.0.
 *
 * Copyright (C) 2024 Stephen Amerige
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
 * Created: December 30, 2024
 */

require_once "vendor/autoload.php";

$general_api_key = "<REPLACE-WITH-YOUR-API-TOKEN>";

// Check input mode: arguments or stdin
if ($argc === 1) {
    // Read lines from stdin
    $input = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (empty($input)) {
        echo "Error: No input provided via stdin.\n";
        exit(1);
    }

    $lines = $input;
} elseif ($argc === 3) {
    // Use arguments directly
    $lines = [sprintf("%s %s", $argv[1], $argv[2])];
} else {
   echo <<<'EOL'
Usage:
  ./create-forward.php <from>@<domain> <to>
  or
  cat input.txt | ./create-forward.php
EOL;
    exit(1);
}

// Initialize the 20i API services
$services_api = new \TwentyI\API\Services($general_api_key);

// Parse and validate input
$forwards = [];
foreach ($lines as $line) {
    $parts = preg_split('/\s+/', $line);
    if (count($parts) !== 2) {
        echo "Error: Invalid line format - '$line'. Expected '<from>@<domain> <to>@<to_domain>'.\n";
        continue;
    }

    list($from_email, $to_email) = $parts;

    if (!filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid 'from' email address - '$from_email'.\n";
        continue;
    }

    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid 'to' email address - '$to_email'.\n";
        continue;
    }

    list($local, $domain) = explode('@', $from_email);
    $forwards[$domain][] = [
        "local" => $local,
        "remote" => $to_email,
        "original_line" => $line,
    ];
}

// Sort domains for efficient package ID changes
ksort($forwards);

// Process forwards domain by domain
foreach ($forwards as $domain => $entries) {
    // Get the package ID for the domain
    try {
        $response = $services_api->getWithFields("/package");
        $packages = json_decode(json_encode($response), true);

        $package_id = null;
        foreach ($packages as $package) {
            if (isset($package['names']) && in_array($domain, $package['names'])) {
                $package_id = $package['id'];
                break;
            }
        }

        if (!$package_id) {
            echo "Error: Package ID not found for domain '$domain'. Skipping related entries.\n";
            continue;
        }
    } catch (Exception $e) {
        echo "Error: Failed to fetch package ID for '$domain' - " . $e->getMessage() . "\n";
        continue;
    }

    // Create email forwards for the domain
    foreach ($entries as $entry) {
        try {
            $services_api->postWithFields(
                "/package/" . $package_id . "/email/" . $domain,
                [
                    "new" => [
                        "forward" => [
                            "local" => $entry["local"],
                            "remote" => $entry["remote"]
                        ]
                    ]
                ]
            );

            echo "Created forward: {$entry['original_line']}\n";
        } catch (Exception $e) {
            echo "Error: Failed to create email forward for '{$entry['original_line']}' - " . $e->getMessage() . "\n";
        }
    }
}
