<?php

function database_additional_pages_create(mysqli $db, int $id, string $name): int
{
    $stmt = db_execute_stmt($db, "INSERT INTO additional_pages (id, name) VALUES (?, ?)", [$id, $name]);
    return $stmt->insert_id;
}

function database_additional_pages_exists_by_id(mysqli $db, int $id): bool
{
    $row = db_query_row($db, "SELECT COUNT(*) FROM additional_pages WHERE id = ?;", [$id]);
    return $row['COUNT(*)'] === 1;
}
