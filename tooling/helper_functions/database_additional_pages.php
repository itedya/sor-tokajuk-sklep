<?php

function database_additional_pages_create(mysqli $db, string $id, string $name): int
{
    $stmt = db_execute_stmt($db, "INSERT INTO additional_pages (id, name) VALUES (?, ?)", [$id, $name]);
    return $stmt->insert_id;
}

function database_additional_pages_exists_by_id(mysqli $db, string $id): bool
{
    $row = db_query_row($db, "SELECT COUNT(*) FROM additional_pages WHERE id = ?;", [$id]);
    return $row['COUNT(*)'] === 1;
}

function database_additional_pages_get(mysqli $db): array
{
    return db_query_rows($db, "SELECT * FROM additional_pages", []);
}

function database_additional_pages_get_by_id(mysqli $db, string $id): ?array
{
    return db_query_row($db, "SELECT * FROM additional_pages WHERE id = ?", [$id]);
}

function database_additional_pages_delete_by_id(mysqli $db, string $id): int
{
    $stmt = db_execute_stmt($db, "DELETE FROM additional_pages WHERE id = ?", [$id]);
    return $stmt->affected_rows;
}

function database_additional_pages_update(mysqli $db, string $id, string $name): int
{
    $stmt = db_execute_stmt($db, "UPDATE additional_pages SET name = ? WHERE id = ?", [$name, $id]);
    return $stmt->affected_rows;
}