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
    $queries = array_filter($queries, fn($query) => !empty($query));
    $queries = array_map(fn($query) => str_replace("\n", '', $query), $queries);
    $queries = array_filter($queries, fn($query) => !empty($query));


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

    $files = glob(__DIR__ . '/../../images/*');

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
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

    $addresses = require __DIR__ . '/../seeding/addresses-data.php';
    foreach ($addresses as $address) {
        db_execute_stmt(
            $db,
            "INSERT INTO addresses (id, user_id, first_line, second_line, city, postal_code) VALUES (?, ?, ?, ?, ?, ?)",
            array_values($address)
        );
    }

    $paymentTypes = require __DIR__ . '/../seeding/payment-types-data.php';
    foreach ($paymentTypes as $paymentType) {
        db_execute_stmt($db, "INSERT INTO payment_types (id, name) VALUES (?, ?)", array_values($paymentType));
    }

    $categories = require __DIR__ . '/../seeding/categories-data.php';
    foreach ($categories as $category) {
        db_execute_stmt($db, "INSERT INTO categories (id, name) VALUES (?, ?)", array_values($category));
    }

    $products = require __DIR__ . '/../seeding/products-data.php';
    foreach ($products as $product) {
        db_execute_stmt($db, "INSERT INTO products (id, name, category_id, description, price) VALUES (?, ?, ?, ?, ?)", array_values($product));
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

    $deliveryMethodsData = require __DIR__ . '/../seeding/delivery-methods-data.php';
    foreach ($deliveryMethodsData as $deliveryMethod) {
        db_execute_stmt($db, "INSERT INTO delivery_methods (id, name, price) VALUES (?, ?, ?)", array_values($deliveryMethod));
    }

    $orders = require __DIR__ . '/../seeding/orders-data.php';
    foreach ($orders as $order) {
        db_execute_stmt(
            $db,
            "INSERT INTO orders (id, user_id, address_id, payment_type_id, status, delivery_method_id, delivery_address_id) VALUES (?, ?, ?, ?, ?, ?, ?)",
            array_values($order)
        );
    }

    $orderProducts = require __DIR__ . '/../seeding/order-products-data.php';
    foreach ($orderProducts as $orderProduct) {
        db_execute_stmt(
            $db,
            "INSERT INTO orders_have_products (order_id, product_id, quantity) VALUES (?, ?, ?)",
            array_values($orderProduct)
        );
    }

    $favouriteProducts = require __DIR__ . '/../seeding/favourite-products-data.php';
    foreach ($favouriteProducts as $favouriteProduct) {
        db_execute_stmt(
            $db,
            "INSERT INTO users_favourite_products (user_id, product_id) VALUES (?, ?)",
            array_values($favouriteProduct)
        );
    }
}
