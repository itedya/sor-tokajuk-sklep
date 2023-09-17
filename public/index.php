<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../database/connection.php";
require_once "../helpers/dump-and-die.php";
require_once "../helpers/display-view.php";
require_once "../app/controllers/auth.controller.php";
require_once "../app/route.php";
require_once "../app/router.php";

require_once "../routes/web.php";
require_once "../routes/api.php";

// Report all PHP errors

function __start()
{
    $route = Router::match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

    if ($route !== null) {
        $route->execute();
    } else {
        http_response_code(404);
        echo "Not found";
    }
}

try {
    __start();
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}
