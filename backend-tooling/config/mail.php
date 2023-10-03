<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "mail.php")) {
    http_response_code(404);
    die();
}

return [
    'host' => '',
    'port' => 0,
    'username' => '',
    'password' => '',
    'from_address' => '',
    'from_name' => ''
];