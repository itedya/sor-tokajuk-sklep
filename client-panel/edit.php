<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

if (session_has("user_edited")) {
    ob_start(); ?>
        <div class="w-full flex flex-col gap-8 h-full justify-center items-center">
            <h2 class="text-3xl text-center text-neutral-300">Pomyślnie zaktualizowałeś swoje dane.</h2>
            <div class="flex justify-center items-center">
                <button hx-get="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>"
                        hx-trigger="click"
                        hx-swap="innerHTML"
                        hx-target="#swappable-panel"
                        class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Ok
                </button>
            </div>
        </div>
    <?php
    echo ob_get_clean();
    return;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = $_POST['email'] ?? null;

    if ($email === null) validation_errors_add("email", "Email nie może być pusty.");

    if (!validation_errors_is_empty()) {
        redirect_and_kill(base_url('/client-panel/edit.php'));
    }

    db_transaction(function (mysqli $db) use ($email) {
        $user = database_users_get_by_id($db, auth_get_user_id());

        database_users_update($db, $user['id'], $email, $user['password'], $user['is_admin'], $user['is_verified']);
    });

    session_flash("user_edited", true);
    redirect_and_kill(base_url('/client-panel/edit.php'));
}

$user = database_users_get_by_id(get_db_connection(), auth_get_user_id());
if (!old_input_has("email")) old_input_add("email", $user['email']);

ob_start(); ?>
    <div class="w-full flex flex-col gap-4">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Edytuj użytkownika
        </div>

        <form method="POST"
              class="flex flex-col gap-4 mx-auto w-full">
            <?= render_textfield(
                label: "Email",
                name: "email",
                type: "email"
            ) ?>
        </form>

        <div class="flex flex-row justify-end items-center gap-4">
            <button hx-post="<?= htmlspecialchars(base_url('/client-panel/edit.php')) ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    hx-include="form"
                    class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg">
                Edytuj
            </button>
        </div>
    </div>
<?php
echo ob_get_clean();
