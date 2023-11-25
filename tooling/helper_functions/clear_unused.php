<?php

function clear_unused_categories(mysqli $db): void
{
    $query = <<<SQL
SELECT category_id
FROM (SELECT categories.id as category_id, pi.id as element_exists
      FROM categories
               LEFT JOIN sklep.products pi on categories.id = pi.category_id) e
WHERE element_exists IS NULL;
SQL;


    $ids = array_map(fn($row) => $row['category_id'], db_query_rows($db, $query, []));

    if (count($ids) > 0) {
        $injection = '(' . implode(',', array_map(fn() => "?", $ids)) . ')';

        db_execute_stmt($db, "DELETE FROM categories WHERE id IN {$injection};", $ids);
    }
}

function clear_unused_images(mysqli $db): void
{
    $images = array_map(fn($row) => $row['image'], db_query_rows($db, "SELECT image FROM products_images;", []));

    $allImages = scandir(__DIR__ . "/../../images");
    $allImages = array_filter($allImages, fn($image) => !in_array($image, ['.', '..', '.gitignore']));

    $images = array_filter($allImages, fn($image) => !in_array($image, $images));

    if (count($images) > 0) {
        foreach ($images as $image) {
            unlink(__DIR__ . "/../../images/{$image}");
        }
    }
}

function clear_unused_parameters(mysqli $db): void
{
    $query = "SELECT id FROM (SELECT parameters.id, pi.value FROM parameters LEFT JOIN products_have_parameters pi ON parameters.id = pi.parameter_id) e WHERE value IS NULL;";

    $ids = array_map(fn($row) => $row['id'], db_query_rows($db, $query, []));

    if (count($ids) > 0) {
        $injection = '(' . implode(',', array_map(fn() => "?", $ids)) . ')';

        db_execute_stmt($db, "DELETE FROM parameters WHERE id IN {$injection};", $ids);
    }
}