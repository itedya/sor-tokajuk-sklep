<?php

function database_order_details_create(mysqli $db, int $orderId, int $productId, int $quantity): int
{
    $query = "INSERT INTO `orders_have_products` (`order_id`, `product_id`, `quantity`) VALUES (?, ?, ?)";
    $stmt = db_execute_stmt($db, $query, [$orderId, $productId, $quantity]);
    return $stmt->insert_id;
}