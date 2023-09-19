<?php

function loadFrontendTooling($toRoot)
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "Component.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "Navbar.php"]);
    require_once join(DIRECTORY_SEPARATOR, [$toRoot, "frontend-tooling", "components", "Layout.php"]);
}