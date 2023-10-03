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

function loadFrontendTooling(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "components", "Component.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "components", "Navbar.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "components", "Layout.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "components", "ErrorMessage.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "components", "MobileNavbarItem.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "components", "NavbarItem.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "facades", "ValidationErrorFacade.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "facades", "OldInputFacade.php"]);
    require_once join(DIRECTORY_SEPARATOR, [__DIR__, "facades", "AuthorizationFacade.php"]);
}

checkIfLoadedStraightfordwardly(__FILE__);