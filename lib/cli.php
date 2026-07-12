<?php
/**
 * Shared command-line helpers for the 20i CLI tools.
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

namespace SoftwareWrap\TwentyI\Cli;

use RuntimeException;

const EXIT_SUCCESS = 0;
const EXIT_ERROR = 1;
const EXIT_CANCELLED = 2;
const EXIT_PARTIAL_FAILURE = 3;

/**
 * Write an error message and terminate.
 */
function fail(string $message, int $exitCode = EXIT_ERROR): void
{
    fwrite(STDERR, "Error: {$message}\n");
    exit($exitCode);
}

/**
 * Read nonempty, noncomment lines from standard input.
 *
 * Lines are trimmed. Blank lines and lines whose first non-whitespace
 * character is # are ignored.
 *
 * @return array<int,string>
 */
function readLinesFromStdin(): array
{
    $lines = [];

    while (($line = fgets(STDIN)) !== false) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        $lines[] = $line;
    }

    return $lines;
}

/**
 * Ask the operator to confirm an action using the controlling terminal.
 *
 * Reading from /dev/tty allows commands to prompt even when standard input
 * has been redirected from a file.
 */
function confirm(string $prompt): bool
{
    $tty = fopen('/dev/tty', 'r+');

    if ($tty === false) {
        throw new RuntimeException(
            'Unable to open /dev/tty for confirmation. '
            . 'Rerun with --yes to bypass the prompt.'
        );
    }

    fwrite($tty, $prompt);

    $response = fgets($tty);
    fclose($tty);

    if ($response === false) {
        return false;
    }

    $response = strtolower(trim($response));

    return $response === 'y' || $response === 'yes';
}
