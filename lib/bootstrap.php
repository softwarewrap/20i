<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/config.php';

$api_library = __DIR__ . '/20i-api-modules/lib/TwentyI/API';

require_once $api_library . '/Exception.php';
require_once $api_library . '/CurlException.php';
require_once $api_library . '/HTTPException.php';
require_once $api_library . '/REST.php';
require_once $api_library . '/Authentication.php';
require_once $api_library . '/ControlPanel.php';
require_once $api_library . '/Services.php';
