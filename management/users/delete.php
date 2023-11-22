<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // get id
    if (!isset($_POST['id'])) redirect_and_kill(base_url("/management/users.php"));
    $id = $_POST['id'];
    if (!is_numeric($id)) redirect_and_kill(base_url("/management/users.php"));
    $id = intval($id);

    db_transaction(function (mysqli $db) use ($id) {
        $user = db_query_row($db, "SELECT * FROM users WHERE id = ?", [$id]);
        if ($user === null) redirect_and_kill(base_url("/management/users.php"));

        db_execute_stmt($db, "DELETE FROM users WHERE id = ?", [$id]);
    });

    redirect_and_kill(base_url("/management/users.php"));
} else {

}