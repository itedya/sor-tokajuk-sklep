<?php

include_once "./database/connection.php";

function __start()
{
}

try {
    __start();
} catch (Exception $e) {
    http_response_code(500);
    echo "Internal server error";
}
