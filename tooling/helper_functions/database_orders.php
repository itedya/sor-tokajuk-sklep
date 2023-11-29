<?php

function database_orders_get_by_user_id(mysqli $db, int $userId): array
{
    $query = "SELECT * FROM `orders` WHERE `user_id` = ?";
    return db_query_rows($db, $query, [$userId]);
}

function database_orders_get(mysqli $db): array
{
    $query = "SELECT * FROM `orders`";
    return db_query_rows($db, $query, []);
}

function database_orders_get_by_id(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM `orders` WHERE id = ?", [$id]);
}

function database_orders_update_status(mysqli $db, int $id, int $status): int
{
    $stmt = db_execute_stmt($db, "UPDATE `orders` SET `status` = ? WHERE `id` = ?", [$status, $id]);
    return $stmt->affected_rows;
}

function database_orders_create(mysqli $db, ?int $userId, int $deliveryMethodId, int $paymentTypeId, int $deliveryAddressId, int $addressId): int
{
    $columns = ["user_id", "status", "delivery_method_id", "payment_type_id", "delivery_address_id", "address_id"];
    $values = [$userId, 0, $deliveryMethodId, $paymentTypeId, $deliveryAddressId, $addressId];

    if ($userId === null) {
        array_shift($columns);
        array_shift($values);
    }

    $compiledColumns = "(" . join(', ', $columns) . ")";
    $compiledValues = "(" . join(', ', array_fill(0, count($values), '?')) . ")";

    $query = sprintf("INSERT INTO `orders` %s VALUES %s;", $compiledColumns, $compiledValues);
    $stmt = db_execute_stmt($db, $query, $values);
    return $stmt->insert_id;
}