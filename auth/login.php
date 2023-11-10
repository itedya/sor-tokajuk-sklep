<?php

require_once '../tooling/autoload.php';

gate_redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['email'])) validation_errors_add('email', 'Email jest wymagany!');
    if (!isset($_POST['password'])) validation_errors_add('password', 'Hasło jest wymagane!');

    if (!validation_errors_is_empty()) redirect_and_kill("login.php");

    $email = $_POST['email'];
    $password = $_POST['password'];

    old_input_add("email", $email);

    // validate email
    $emailError = validate_email($email);
    if ($emailError !== null) validation_errors_add("email", $emailError);

    // validate password
    $passwordError = validate_password($password);
    if ($passwordError !== null) validation_errors_add("password", $passwordError);

    if (!validation_errors_is_empty()) redirect_and_kill("login.php");

    $db = get_db_connection();
    $data = db_query_row($db, "SELECT id, is_verified, password FROM users WHERE email = ?", [$email]);

    if ($data === null) {
        validation_errors_add("email", "Dane do logowanie nie zgadzają się.");
        redirect_and_kill("login.php");
    }

    if ($data['is_verified'] === 0) {
        validation_errors_add("email", "Konto nie zostało jeszcze zweryfikowane.");
        redirect_and_kill("login.php");
    }

    if (!password_verify($password, $data['password'])) {
        validation_errors_add('email', 'Dane do logowania nie zgadzają się.');
        redirect_and_kill("login.php");
    }

    auth_login($data['id']);

    redirect_and_kill(config("app.url") . "/");
}

echo render_in_layout(function () {
    ?>
    <div class="flex justify-center items-center p-4">
        <form method="POST" action="/auth/login.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Logowanie</h1>

            <div class="flex flex-col gap-4">
                <?= render_textfield(label: 'Email', name: 'email', type: 'text') ?>
                <?= render_textfield(label: 'Hasło', name: 'password', type: 'password') ?>
            </div>

            <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zaloguj</button>
                <a class="text-center w-full text-blue-200 sm:w-auto" href="/auth/forgot-password.php">Nie pamiętam
                    hasła</a>
            </div>
        </form>
    </div>
    <?php
});

?>