<?php

function database_products_images_get_by_product_id(mysqli $db, int $productId): array
{
    return db_query_rows($db, "SELECT * FROM products_images WHERE product_id = ?", [$productId]);
}