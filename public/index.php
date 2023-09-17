<?php

require_once "../database/connection.php";
require_once "../helpers/dump-and-die.php";
require_once "../app/route.php";
require_once "../app/router.php";

require_once "../routes/web.php";
require_once "../routes/api.php";

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
