<?php
require_once __DIR__ . '/config.php';
$result = $conn->query('SELECT 1');
if (!$result) {
    http_response_code(500);
    exit('DB_CONNECTION_FAILED');
}
http_response_code(200);
echo 'DB_CONNECTION_OK';
