<?php

require_once __DIR__ . '/env.php';

load_env(__DIR__ . '/../.env');

$api_key = $_ENV['API_KEY'] ?? '';

if ($api_key === '') {
    throw new RuntimeException(
        'API_KEY is not defined in .env.'
    );
}
