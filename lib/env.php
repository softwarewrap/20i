<?php

function load_env(string $filename): void
{
    if (!file_exists($filename)) {
        return;
    }

    foreach (file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || $line[0] === '#') {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');

        $_ENV[trim($key)] = trim($value);
    }
}
