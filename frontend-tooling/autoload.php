<?php

function loadFrontendTooling($toRoot)
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "Component.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "Navbar.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "Layout.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "ErrorMessage.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "MobileNavbarItem.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "NavbarItem.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "facades", "ValidationErrorFacade.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "facades", "OldInputFacade.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "facades", "AuthorizationFacade.php"]);
}

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "autoload.php")) {
    http_response_code(404);
    die();
}