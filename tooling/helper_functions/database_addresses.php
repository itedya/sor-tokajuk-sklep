<?php

function database_addresses_get_by_id_with_deleted(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM addresses WHERE id = ?", [$id]);
}

function database_addresses_get_by_id(mysqli $db, int $id): ?array {
    return db_query_row($db, "SELECT * FROM addresses WHERE id = ? AND deleted_at IS NULL", [$id]);
}

function database_addresses_get_by_user_id(mysqli $db, int $userId): ?array
{
    return db_query_rows($db, "SELECT * FROM addresses WHERE user_id = ? AND deleted_at IS NULL", [$userId]);
}

function database_addresses_delete_by_id(mysqli $db, int $id): void
{
    db_execute_stmt($db, "UPDATE addresses SET deleted_at = NOW() WHERE id = ?", [$id]);
}