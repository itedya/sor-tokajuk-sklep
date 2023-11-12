<?php

function get_db_connection()
{
    $conn = new mysqli(config("database.host"), config("database.username"), config("database.password"), config("database.database"));

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function db_transaction(callable $callback, ?callable $catchCallback = null)
{
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

function db_execute_stmt(mysqli $db, string $query, array $parameters): mysqli_stmt
{
    $stmt = $db->prepare($query);

    $types = "";

    foreach ($parameters as $parameterValue) {
        $type = match (gettype($parameterValue)) {
            "string" => "s",
            "integer" => "i",
            "double", "boolean" => "d",
            default => throw new Exception("Nieznany typ zmiennej, dodaj taki: " . gettype($parameterValue))
        };

        $types .= $type;
    }

    if (count($parameters) !== 0) {
        $stmt->bind_param($types, ...array_values($parameters));
    }

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

function db_migrate(mysqli $db): void
{
    $sql = file_get_contents(__DIR__ . '/../../database.sql');

    $queries = explode(";", $sql);
    $queries = array_filter($queries, fn($query) => $query !== "");

    foreach ($queries as $query) {
        db_execute_stmt($db, $query, []);
    }
}

function db_drop(mysqli $db): void
{
    $selectDropsSQL = <<<SQL
    SELECT CONCAT('DROP TABLE IF EXISTS `', table_name, '`;') as text
    FROM information_schema.tables
    WHERE table_schema = ?;
SQL;


    $queries = db_query_rows($db, $selectDropsSQL, [config("database.database")]);
    $queries = array_map(fn($q) => $q['text'], $queries);

    db_execute_stmt($db, "SET FOREIGN_KEY_CHECKS = 0;", []);

    foreach ($queries as $query) {
        db_execute_stmt($db, $query, []);
    }

    db_execute_stmt($db, "SET FOREIGN_KEY_CHECKS = 1;", []);
}

function db_seed(mysqli $db): void
{
    $users = require __DIR__ . '/../seeding/users-data.php';
    foreach ($users as $user) {
        db_execute_stmt(
            $db,
            "INSERT INTO users (id, email, password, is_verified, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?)",
            array_values($user)
        );
    }

    $products = require __DIR__ . '/../seeding/products-data.php';
    foreach ($products as $product) {
        db_execute_stmt($db, "INSERT INTO products (id, name, description, price) VALUES (?, ?, ?, ?)", array_values($product));
    }

    $productsImages = require __DIR__ . '/../seeding/products-images-data.php';

    foreach ($productsImages as $productImage) {
        $productImageName = uniqid("product_image_") . ".png";

        file_put_contents(__DIR__ . "/../../images/" . $productImageName, $productImage['image']);

        db_execute_stmt($db,
            "INSERT INTO products_images (id, product_id, image, created_at) VALUES (?, ?, ?, ?)",
            [$productImage['id'], $productImage['product_id'], $productImageName, $productImage['created_at']]
        );
    }
}