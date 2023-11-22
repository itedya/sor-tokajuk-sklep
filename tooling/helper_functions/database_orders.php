<?php

function database_orders_get_by_user_id(mysqli $db, int $userId): array
{
    $query = "SELECT * FROM `orders` WHERE `user_id` = ?";
    return db_query_rows($db, $query, [$userId]);
}