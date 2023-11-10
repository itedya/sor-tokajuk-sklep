<?php

require_once '../tooling/autoload.php';

gate_redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['email'])) validation_errors_add("email", "Email jest wymagany");
    else validation_errors_add("email", $_POST['email']);

    if (!isset($_POST['password'])) validation_errors_add("password", "Hasło jest wymagane");
    if (!isset($_POST['repeat_password'])) validation_errors_add("repeat_password", "Powtórzenie hasła jest wymagane");

    if (!validation_errors_is_empty()) return;

    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        validation_errors_add("email", "Email jest niepoprawny");
    }

    if (strlen($password) < 8) {
        validation_errors_add("password", "Hasło musi mieć co najmniej 8 znaków");
    }

    if ($password !== $repeat_password) {
        validation_errors_add("repeat_password", "Hasła muszą być takie same");
    }

    if (!validation_errors_is_empty()) redirect_and_kill("register.php");

    db_transaction(function (mysqli $db) use ($email, $password, &$id, &$hash) {
        $row = db_query_row($db, "SELECT * FROM users WHERE email = ?", [$email]);
        if ($row !== null) {
            validation_errors_add("email", "Użytkownik o tym adresie email już istnieje.");
            redirect_and_kill("register.php");
        }

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = db_execute_stmt($db, "INSERT INTO users (email, `password`) VALUES (?, ?);", [$email, $passwordHash]);
        $id = $stmt->insert_id;
        $stmt->close();

        $hash = uniqid("email_verification_");

        $stmt = db_execute_stmt($db, "INSERT INTO email_verification_attempts (`user_id`, `hash`) VALUES (?, ?);", [$id, $hash]);
        $stmt->close();
    });

    sendMail($email, 'Potwierdź email do konta', '<a href="' . config("app.url") . '/auth/confirm-email.php?hash=' . $hash . '">Kliknij tutaj aby potwierdzić hasło</a>');

    auth_login($id);

    session_flash('after_registration', true);
    redirect_and_kill("../index.php");
}

echo render_in_layout(function () { ?>
    <script>
        window.addEventListener("DOMContentLoaded", () => {
            document.querySelector("#register-form").addEventListener("submit", (e) => {
                const button = document.getElementById("register-button");
                button.disabled = true;
                button.innerText = "Rejestrowanie...";
            });
        });
    </script>

    <div class="flex justify-center items-center p-4">
        <form method="POST" action="/auth/register.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl"
              id="register-form">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Rejestracja</h1>

            <div class="flex flex-col gap-4">
                <?= render_textfield(label: "Email", type: 'email', name: 'email') ?>
                <?= render_textfield(label: "Hasło", type: 'password', name: 'password') ?>
                <?= render_textfield(label: "Powtórz hasło", type: 'repeat_password', name: 'repeat_password') ?>
            </div>

            <div class="flex justify-end">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg disabled:bg-blue-400 duration-300"
                        id="register-button">Zarejestruj się
                </button>
            </div>
        </form>
    </div>
<?php });