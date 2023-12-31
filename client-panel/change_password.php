<?php

require_once __DIR__ . "/../tooling/autoload.php";

gate_redirect_if_unauthorized();

if (session_has("user_edited")) {
    ob_start(); ?>
    <div class="w-full flex flex-col gap-8 h-full justify-center items-center">
        <h2 class="text-3xl text-center text-neutral-300">Pomyślnie zaktualizowałeś swoje hasło.</h2>
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
    $password = $_POST['password'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;
    $newPasswordConfirmation = $_POST['new_password_confirmation'] ?? null;

    if ($password === null) validation_errors_add("password", "Stare hasło nie może być puste.");
    if ($newPassword === null) validation_errors_add("new_password", "Nowe hasło nie może być puste.");
    if ($newPasswordConfirmation === null) validation_errors_add("new_password_confirmation", "Powtórzone hasło nie może być puste.");

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);

    $db = get_db_connection();

    $user = database_users_get_by_id($db, auth_get_user_id());

    if (!password_verify($password, $user['password'])) {
        validation_errors_add("password", "Stare hasło jest nieprawidłowe.");
        redirect_and_kill($_SERVER['REQUEST_URI']);
    }

    if ($newPassword !== $newPasswordConfirmation) {
        validation_errors_add("new_password_confirmation", "Powtórzone hasło nie jest takie samo jak nowe hasło.");
        redirect_and_kill($_SERVER['REQUEST_URI']);
    }

    $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    db_transaction(function (mysqli $db) use ($user, $passwordHash) {
        database_users_update($db, $user['id'], $user['email'], $passwordHash, $user['is_admin'], $user['is_verified']);
    });

    session_flash("user_edited", true);
    redirect_and_kill($_SERVER['REQUEST_URI']);
}

$user = database_users_get_by_id(get_db_connection(), auth_get_user_id());

ob_start(); ?>
    <div class="w-full flex flex-col gap-4">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Zmień hasło
        </div>

        <form class="flex flex-col gap-4 mx-auto w-full">
            <?= render_textfield(
                label: "Stare hasło",
                name: "password",
                type: "password"
            ) ?>

            <?= render_textfield(
                label: "Nowe hasło",
                name: "new_password",
                type: "password"
            ) ?>

            <?= render_textfield(
                label: "Powtórz nowe hasło",
                name: "new_password_confirmation",
                type: "password"
            ) ?>
        </form>

        <div class="flex justify-center items-center">
            <button hx-post="<?= $_SERVER['REQUEST_URI'] ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    hx-include="form"
                    class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                Zapisz
            </button>
        </div>
    </div>
<?php
echo ob_get_clean();
