<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "database.php")) {
    http_response_code(404);
    die();
}

return [
    'host' => 'localhost',
    'username' => 'sklep',
    'password' => '',
    'database' => 'sklep',
];