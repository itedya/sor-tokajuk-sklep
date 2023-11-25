<?php

function database_payment_types_create(mysqli $db, string $name): void
{
    db_execute_stmt($db, "INSERT INTO payment_types (name) VALUES (?)", [$name]);
}

function database_payment_types_does_exist_by_name(mysqli $db, string $name): bool
{
    return db_query_row($db, "SELECT id FROM payment_types WHERE deleted_at IS NULL AND name = ?", [$name]) !== null;
}

function database_payment_types_get(mysqli $db): array
{
    return db_query_rows($db, "SELECT * FROM payment_types WHERE deleted_at IS NULL", []);
}

function database_payment_types_get_by_id(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM payment_types WHERE deleted_at IS NULL AND id = ?", [$id]);
}

function database_payment_type_get_by_id_with_deleted(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM payment_types WHERE id = ?", [$id]);
}

function database_payment_types_delete_by_id(mysqli $db, int $id): void
{
    db_execute_stmt($db, "UPDATE payment_types SET deleted_at = NOW() WHERE id = ?", [$id]);
}
