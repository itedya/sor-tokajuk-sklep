<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_has("user_edited")) {
    echo render_in_layout(function () { ?>
        <div class="container p-4 flex flex-col gap-8 mx-auto">
            <h2 class="text-3xl text-center text-neutral-300">Użytkownik został zaktualizowany.</h2>
            <div class="flex justify-center items-center">
                <a href="<?= config("app.url") . "/management/users.php" ?>"
                   class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do użytkowników
                </a>
            </div>
        </div>
    <?php });

    redirect_and_kill($_SERVER['REQUEST_URI']);
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_POST['id'] ?? null;
    $email = $_POST['email'] ?? null;
    $is_admin = $_POST['is_admin'] ?? null;

    if ($id === null) validation_errors_add("id", "Id nie może być puste.");
    if ($email === null) validation_errors_add("email", "Email nie może być pusty.");
    if ($is_admin === null) validation_errors_add("is_admin", "Rola nie może być pusta.");

    if (!validation_errors_is_empty()) {
        redirect_and_kill(base_url("/management/users/edit.php", ['id' => $id]));
    }

    $is_admin = intval($is_admin);
    if ($is_admin !== 0 && $is_admin !== 1) {
        validation_errors_add("is_admin", "Pole rola posiada niepoprawną wartość.");
        redirect_and_kill(base_url("/management/users/edit.php", ['id' => $id]));
    }

    db_transaction(function (mysqli $db) use ($id, $email, $is_admin) {
        $user = db_query_row($db, "SELECT * FROM users WHERE id = ?", [$id]);
        if ($user === null) redirect_and_kill(base_url("/management/users.php"));

        db_execute_stmt($db, "UPDATE users SET email = ?, is_admin = ? WHERE id = ?", [$email, $is_admin, $id]);
    });

    session_flash("user_edited", true);
    redirect_and_kill(base_url("/management/users/edit.php", ['id' => $id]));
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(base_url("/management/users.php"));

$users = db_query_row(get_db_connection(), "SELECT * FROM users WHERE id = ?", [$id]);
if ($users === null) redirect_and_kill(base_url("/management/users.php"));

if (!old_input_has("email")) old_input_add("email", $users['email']);
if (!old_input_has("is_admin")) old_input_add("is_admin", $users['is_admin']);
else old_input_add("is_admin", intval(old_input_get('is_admin')));

echo render_in_layout(function () use ($users) { ?>
    <div class="container mx-auto p-4 gap-8 flex flex-col">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Edytuj użytkownika
        </div>

        <form action="<?= base_url("/management/users/edit.php") ?>" method="POST"
              class="flex flex-col gap-4 mx-auto w-full md:w-1/2">
            <input type="hidden" name="id" value="<?= $users['id'] ?>"/>

            <?= render_textfield(
                label: "Email",
                name: "email",
                type: "email"
            ) ?>

            <?= render_select(
                label: "Rola",
                name: "is_admin",
                options: [
                    ['value' => 0, 'text' => 'Klient'],
                    ['value' => 1, 'text' => 'Pracownik'],
                ]
            ) ?>

            <div class="flex flex-row justify-end items-center gap-4">
                <a href="<?= config("app.url") . "/management/users.php" ?>"
                   class="px-8 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do użytkowników
                </a>

                <button type="submit" class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg">
                    Edytuj
                </button>
            </div>
        </form>
    </div>
<?php });