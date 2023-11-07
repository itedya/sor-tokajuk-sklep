<?php

function get_db_connection()
{
    $conn = new mysqli(config("database.host"), config("database.username"), config("database.password"), config("database.database"));

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function db_transaction(callable $callback, ?callable $catchCallback = null) {
    $db = get_db_connection();

    try {
        $callback($db);

        $db->commit();
    } catch (Exception $e) {
        $db->rollback();

        if ($catchCallback !== null) $catchCallback($e);
        else throw $e;
    }
}

function db_execute_stmt(mysqli $db, string $query, array $parameters): mysqli_stmt {
    $stmt = $db->prepare($query);

    $types = "";

    foreach ($parameters as $parameterValue) {
        $type = match (gettype($parameterValue)) {
            "string" => "s",
            "integer" => "i",
            "double" => "d",
            default => throw new Exception("Nieznany typ zmiennej, dodaj taki: " . gettype($parameterValue))
        };

        $types .= $type;
    }

    $stmt->bind_param($types, ...array_values($parameters));

    $stmt->execute();

    return $stmt;
}

function db_query_rows(mysqli $db, string $query, array $parameters): array
{
    $stmt = db_execute_stmt($db, $query, $parameters);

    $result = $stmt->get_result();

    $returnedValue = $result->fetch_all(MYSQLI_ASSOC);

    $result->close();
    $stmt->close();

    return $returnedValue;
}

function db_query_row(mysqli $db, string $query, array $parameters): array|null
{
    $stmt = db_execute_stmt($db, $query, $parameters);

    $result = $stmt->get_result();

    $returnedValue = $result->fetch_assoc();

    $result->close();
    $stmt->close();

    return $returnedValue;
}
