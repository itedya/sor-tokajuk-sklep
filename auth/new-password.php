<?php

require_once '../tooling/autoload.php';

gate_redirect_if_logged_in();

// Sprawdź czy nie potrzeba wyświetlić, że link już wygasł
if (get_query_param('expired') === "true") {
    http_response_code(200);
    echo render_in_layout(function () { ?>
        <div class="flex justify-center items-center p-4">
            <div class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
                <h1 class="text-4xl font-bold text-center text-neutral-300">Nieprawidłowy link</h1>

                <div class="flex flex-col gap-4 text-center text-neutral-300">
                    Ten link już wygasł, wygeneruj nowy.
                </div>
            </div>
        </div>
    <?php });
    die();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (get_query_param('uuid') === null) {
        redirect_and_kill(config("app.url") . "/");
    }

    $uuid = get_query_param('uuid');

    if ($uuid === false) redirect_and_kill(config("app.url") . "/");

    if (!isset($_POST['new_password'])) {
        validation_errors_add("new_password", "Nowe hasło jest wymagane.");
    }

    if (!isset($_POST['repeat_new_password'])) {
        validation_errors_add("repeat_new_password", "Powtórzenie nowego hasła jest wymagane.");
    }

    if (!validation_errors_is_empty()) {
        return;
    }

    if ($_POST['new_password'] !== $_POST['repeat_new_password']) {
        validation_errors_add("new_password", "Hasła się nie zgadzają.");
        return;
    }

    $new_password = $_POST['new_password'];

    db_transaction(function ($db) use ($new_password, $uuid) {
        $result = db_query_row($db, "SELECT user_id FROM password_resets WHERE uuid = ?", [
            $uuid
        ]);

        if ($result === null) {
            throw new InvalidArgumentException("PWD_RESET_INVALID_UUID");
        }

        $userId = $result['user_id'];

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        db_execute_stmt($db, "UPDATE users SET password = ? WHERE id = ?", [
            $hashed_password,
            $userId
        ]);

        db_execute_stmt($db, "DELETE FROM password_resets WHERE uuid = ?", [
            $uuid
        ]);
    }, function (Exception $e) {
        if ($e instanceof InvalidArgumentException && $e->getMessage() === "PWD_RESET_INVALID_UUID") {
            redirect_and_kill(config("app.url") . "/");
        }

        throw $e;
    });

    echo render_in_layout(function () { ?>
        <div class="flex justify-center items-center p-4">
            <div class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
                <h1 class="text-4xl font-bold text-center text-neutral-300">Sukces</h1>

                <div class="flex flex-col gap-4 text-center text-neutral-300">
                    Twoje hasło zostało zresetowane, możesz już się zalogować.

                    <div class="flex flex-col items-center justify-center gap-4">
                        <a href="./login.php" class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zaloguj
                            się</a>
                    </div>
                </div>
            </div>
        </div>
    <?php });
    die();
} else {
    // Sprawdź czy ten link nie wygasł
    if (get_query_param('uuid') === null) {
        redirect_and_kill(config("app.url") . "/");
    }

    $uuid = base64_decode(get_query_param('uuid'));

    if ($uuid === false) redirect_and_kill(config("app.url") . "/");

    db_transaction(function ($db) use ($uuid) {
        $result = db_query_row($db, "SELECT created_at_timestamp FROM password_resets WHERE uuid = ?", ['uuid' => $uuid]);

        if ($result === null) {
            throw new InvalidArgumentException("PWD_RESET_UUID_INVALID");
        }

        $timestamp = $result['created_at_timestamp'];
        if ($timestamp < time() - (60 * 60 * 1)) {
            throw new InvalidArgumentException("PWD_RESET_SESSION_EXPIRED");
        }
    }, function (Exception $e) {
        if ($e instanceof InvalidArgumentException && $e->getMessage() === "PWD_RESET_UUID_INVALID") {
            redirect_and_kill(config("app.url") . "/");
        }

        if ($e instanceof InvalidArgumentException && $e->getMessage() === "PWD_RESET_SESSION_EXPIRED") {
            header("Location: " . config("app.url") . "/auth/new-password.php?expired=true");
        }

        throw $e;
    });
}

$uuid = get_query_param('uuid');

echo render_in_layout(function () { ?>
    <div class="flex justify-center items-center p-4">
        <form method="POST" action="/auth/new-password.php?uuid=$uuid"
              class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Ustaw nowe hasło</h1>

            <div class="flex flex-col gap-4">
                <?= render_textfield(label: "Nowe hasło", name: "new_password", type: "password") ?>
                <?= render_textfield(label: "Powtórz nowe hasło", name: "repeat_new_password", type: "password") ?>
            </div>

            <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Ustaw nowe hasło
                </button>
            </div>
        </form>
    </div>
<?php });