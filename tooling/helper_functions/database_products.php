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

function database_products_get(mysqli $db): array
{
    $query = <<<SQL
SELECT products.*, pi.image FROM products
LEFT JOIN (
    SELECT id, product_id, image
    FROM products_images pi1
    WHERE id = (
        SELECT id
        FROM products_images pi2
        WHERE pi1.product_id = pi2.product_id
        ORDER BY pi2.id
        LIMIT 1
    )
) pi ON pi.product_id = products.id
SQL;

    return db_query_rows($db, $query, []);
}