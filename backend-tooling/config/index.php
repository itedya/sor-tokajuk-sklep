<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "config.php")) {
    http_response_code(404);
    die();
}

function config(string $path)
{
    $pathParts = explode(".", $path);
    $config = require join(DIRECTORY_SEPARATOR, [__DIR__, $pathParts[0] . ".php"]);

    return $config[$pathParts[1]];
}