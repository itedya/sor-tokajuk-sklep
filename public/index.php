<?php

include_once "../database/connection.php";
include_once "../helpers/dump-and-die.php";

function __start()
{
}

try {
    __start();
} catch (Exception $e) {
    http_response_code(500);
}
