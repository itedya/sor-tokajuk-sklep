<?php

function database_delivery_method_get_by_id_with_deleted(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM delivery_methods WHERE id = ?", [$id]);
}