<?php

function database_delivery_methods_get_by_name(mysqli $db, string $name): ?array
{
    return db_query_row($db, "SELECT * FROM delivery_methods WHERE deleted_at IS NULL and name = ?", [$name]);
}

function database_delivery_methods_create(mysqli $db, string $name, int $price): int
{
    $stmt = db_execute_stmt($db, "INSERT INTO delivery_methods (name, price) VALUES (?, ?)", [$name, $price]);
    return $stmt->insert_id;
}

function database_delivery_methods_update(mysqli $db, int $id, string $name, int $price): void
{
    db_execute_stmt($db, "UPDATE delivery_methods SET name = ?, price = ? WHERE deleted_at IS NULL AND id = ?", [$name, $price, $id]);
}

function database_delivery_methods_delete_by_id(mysqli $db, int $id): int
{
    $stmt = db_execute_stmt($db, "UPDATE delivery_methods SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL", [$id]);
    return $stmt->affected_rows;
}

function database_delivery_methods_get_by_id(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM delivery_methods WHERE id = ? AND deleted_at IS NULL", [$id]);
}

function database_delivery_method_get_by_id_with_deleted(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM delivery_methods WHERE id = ?", [$id]);
}

function database_delivery_methods_get(mysqli $db): array
{
    return db_query_rows($db, "SELECT * FROM delivery_methods WHERE deleted_at IS NULL", []);
}
