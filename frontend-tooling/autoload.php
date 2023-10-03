<?php

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

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "autoload.php")) {
    http_response_code(404);
    die();
}