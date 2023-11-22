<?php

function database_products_get_by_order_id(mysqli $db, int $orderId): array
{
    $query = <<<SQL
SELECT products.*, orders_have_products.quantity
FROM orders_have_products
         INNER JOIN products ON orders_have_products.product_id = products.id
WHERE order_id = ?;
SQL;

    return db_query_rows($db, $query, [$orderId]);
}