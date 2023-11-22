<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_has("user_deleted")) {
    echo render_in_layout(function () { ?>
        <div class="container p-4 flex flex-col gap-8 mx-auto">
            <h2 class="text-3xl text-center text-neutral-300">Użytkownik został usunięty.</h2>
            <div class="flex justify-center items-center">
                <a href="<?= config("app.url") . "/management/users.php" ?>"
                   class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do użytkowników
                </a>
            </div>
        </div>
    <?php });
}

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

    session_flash("user_deleted", true);
    redirect_and_kill(base_url("/management/users.php"));
} else {
    // get id
    if (!isset($_GET['id'])) redirect_and_kill(base_url("/management/users.php"));
    $id = $_GET['id'];
    if (!is_numeric($id)) redirect_and_kill(base_url("/management/users.php"));
    $id = intval($id);

    $result = db_query_row(get_db_connection(), "SELECT id, email FROM users WHERE id = ?", [$id]);
    if ($result === null) redirect_and_kill(base_url("/management/users.php"));

    echo render_in_layout(function () use ($result) { ?>
        <div class="text-3xl text-center text-neutral-300 p-4">
            Czy na pewno chcesz usunąć użytkownika <?= htmlspecialchars($result['email']) ?>?
        </div>

        <div class="flex flex-row justify-center items-center gap-4">
            <form action="<?= base_url("/management/products/delete.php") ?>" method="POST">
                <input type="hidden" name="id" value="<?= $result['id'] ?>"/>

                <button class="px-8 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg">
                    Usuń
                </button>
            </form>

            <a href="<?= base_url("/management/users.php") ?>"
               class="px-8 py-2 bg-neutral-200 text-neutral-900 font-semibold rounded-lg">
                Anuluj
            </a>
        </div>
    <?php });
}