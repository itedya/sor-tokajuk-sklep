<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "database.php")) {
    http_response_code(404);
    die();
}

function getDatabaseConnection()
{
    $conn = new mysqli(config("database.host"), config("database.username"), config("database.password"), config("database.database"));

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}