<?php

if (!function_exists('checkIfLoadedStraightfordwardly')) {
    function checkIfLoadedStraightfordwardly($filepath): void
    {
        if ($_SERVER['SCRIPT_FILENAME'] === $filepath) {
            http_response_code(404);
            die();
        }
    }
}

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

checkIfLoadedStraightfordwardly(__FILE__);