<?php

function database_favourite_products_get_by_user_id_with_image(mysqli $db, int $userId): array
{
    $sql = <<<SQL
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
WHERE products.id IN (SELECT product_id FROM users_favourite_products WHERE user_id = ?);
SQL;

    return db_query_rows($db, $sql, [$userId]);
}

function database_favourite_products_delete_by_user_id_and_product_id(mysqli $db, int $userId, int $productId): void
{
    db_execute_stmt($db, "DELETE FROM users_favourite_products WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
}

function database_favourite_products_exists_by_user_id_and_product_id(mysqli $db, int $userId, int $productId): bool
{
    return db_query_row($db, "SELECT * FROM users_favourite_products WHERE user_id = ? AND product_id = ?", [$userId, $productId]) !== null;
}

function database_favourite_products_create(mysqli $db, int $userId, int $productId): void
{
    db_execute_stmt($db, "INSERT INTO users_favourite_products (user_id, product_id) VALUES (?, ?)", [$userId, $productId]);
}