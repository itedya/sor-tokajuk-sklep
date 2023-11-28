<?php

function database_parameters_get_by_product_id(mysqli $db, int $productId): array
{
    $query = <<<SQL
SELECT parameters.*, products_have_parameters.value
FROM products_have_parameters
    INNER JOIN parameters ON parameters.id = products_have_parameters.parameter_id
WHERE products_have_parameters.product_id = ?
SQL;

    return db_query_rows($db, $query, [$productId]);
}