<?php

function database_categories_get_by_id(mysqli $db, int $id): array
{
    return db_query_row($db, "SELECT * FROM categories WHERE id = ?", [$id]);
}