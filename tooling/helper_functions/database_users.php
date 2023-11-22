<?php

function database_users_get_by_id(mysqli $db, int $id): ?array
{
    $query = "SELECT * FROM `users` WHERE `id` = $id";
    return db_query_row($db, $query, []);
}

function database_users_update(mysqli $db, int $id, string $email, string $password, bool $isAdmin, bool $isVerified): void
{
    $stmt = db_execute_stmt($db, "UPDATE users SET email = ?, password = ?, is_admin = ?, is_verified = ? WHERE id = ?", [$email, $password, $isAdmin, $isVerified, $id]);
}