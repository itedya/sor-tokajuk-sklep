<?php

function loadBackendTooling($toRoot)
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "backend-tooling", "PHPMailer-master", "src", "Exception.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "backend-tooling", "PHPMailer-master", "src", "SMTP.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "backend-tooling", "PHPMailer-master", "src", "PHPMailer.php"]);
}

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "autoload.php")) {
    http_response_code(404);
    die();
}