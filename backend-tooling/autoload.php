<?php

function loadBackendTooling(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "config", "index.php"]);

    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "database.php"]);

    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "PHPMailer-master", "src", "Exception.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "PHPMailer-master", "src", "SMTP.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "PHPMailer-master", "src", "PHPMailer.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "mail.php"]);
}

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "autoload.php")) {
    http_response_code(404);
    die();
}