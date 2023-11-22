<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_has("user_created")) {
    echo render_in_layout(function () { ?>
        <div class="container p-4 flex flex-col gap-8 mx-auto">
            <h2 class="text-3xl text-center text-neutral-300">Pracownik został dodany.</h2>
            <div class="flex justify-center items-center">
                <a href="<?= base_url("/management/users/create.php") ?>"
                   class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do użytkowników
                </a>
            </div>
        </div>
    <?php });

    redirect_and_kill($_SERVER['REQUEST_URI']);
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $repeat_password = $_POST['repeat_password'] ?? null;

    if ($email === null) validation_errors_add("email", "Email nie może być pusty.");
    if ($password === null) validation_errors_add("password", "Hasło nie może być puste.");
    if ($repeat_password === null) validation_errors_add("repeat_password", "Powtórzone hasło nie może być puste.");

    foreach ($_POST as $key => $value) old_input_add($key, $value);

    if (!validation_errors_is_empty()) {
        redirect_and_kill($_SERVER['REQUEST_URI']);
    }

    if ($password !== $repeat_password) {
        validation_errors_add("repeat_password", "Hasła nie są takie same.");
        redirect_and_kill($_SERVER['REQUEST_URI']);
    }

    db_transaction(function (mysqli $db) use ($email, $password) {
        $user = db_query_row($db, "SELECT * FROM users WHERE email = ?", [$email]);
        if ($user !== null) {
            validation_errors_add("email", "Użytkownik o podanym adresie email już istnieje.");
            redirect_and_kill($_SERVER['REQUEST_URI']);
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        db_execute_stmt($db, "INSERT INTO users (email, password, is_admin, is_verified) VALUES (?, ?, ?, ?)", [$email, $password_hash, 1, 1]);
    });

    session_flash("user_created", true);
    redirect_and_kill($_SERVER['REQUEST_URI']);
}

echo render_in_layout(function () { ?>
    <div class="container mx-auto p-4 gap-8 flex flex-col">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Dodaj pracownika
        </div>

        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" class="flex flex-col gap-4 mx-auto w-full lg:w-1/2">
            <?= render_textfield(
                "Email",
                "email",
                "email"
            ) ?>

            <?= render_textfield(
                "Hasło",
                "password",
                "password"
            ) ?>

            <?= render_textfield(
                "Powtórz hasło",
                "repeat_password",
                "password"
            ) ?>

            <div class="flex flex-row justify-end items-center gap-4">
                <button class="px-8 py-2 bg-green-600 text-neutral-200 font-semibold rounded-lg">
                    Dodaj
                </button>
            </div>
        </form>
    </div>
<?php });