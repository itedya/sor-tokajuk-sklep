<?php

function database_users_get_by_id(mysqli $db, int $id): ?array
{
    $query = "SELECT * FROM `users` WHERE `id` = $id";
    return db_query_row($db, $query, []);
}