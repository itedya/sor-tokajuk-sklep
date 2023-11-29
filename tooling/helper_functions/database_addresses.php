<?php

function database_addresses_get_by_id_with_deleted(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM addresses WHERE id = ?", [$id]);
}

function database_addresses_get_by_id(mysqli $db, int $id): ?array
{
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

function database_addresses_update_by_id(mysqli $db, int $id, string $firstLine, string $secondLine, string $city, string $postalCode): void
{
    db_execute_stmt($db, "UPDATE addresses SET first_line = ?, second_line = ?, city = ?, postal_code = ? WHERE id = ?", [$firstLine, $secondLine, $city, $postalCode, $id]);
}

function database_addresses_create(mysqli $db, ?int $userId, string $firstLine, string $secondLine, string $city, string $postalCode): int
{
    $stmt = db_execute_stmt($db, "INSERT INTO addresses (user_id, first_line, second_line, city, postal_code) VALUES (?, ?, ?, ?, ?)", [$userId, $firstLine, $secondLine, $city, $postalCode]);
    return $stmt->insert_id;
}