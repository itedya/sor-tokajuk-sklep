<?php

function database_payment_type_get_by_id_with_deleted(mysqli $db, int $id): ?array
{
    return db_query_row($db, "SELECT * FROM payment_types WHERE id = ?", [$id]);
}