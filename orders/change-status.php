<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$id = $_GET['id'] ?? null;
if (!is_numeric($id)) abort(404);

$db = get_db_connection();
$order = database_orders_get_by_id($db, $id);

if ($order === null) abort(404);

database_orders_update_status($db, $id, $order['status'] === 0 ? 1 : 0);

$db->close();

redirect_and_kill($_GET['previous_page'] ?? base_url('/orders/details.php', ['id' => $id]));
