<?php

function getDatabaseConnection()
{
    $conn = new mysqli(config("database.host"), config("database.username"), config("database.password"), config("database.database"));

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}