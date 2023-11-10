<?php

require_once '../tooling/autoload.php';

gate_redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['email'])) validation_errors_add("email", "Pole email jest wymagane.");
    if (!validation_errors_is_empty()) redirect_and_kill("forgot-password.php");

    $email = $_POST['email'];

    old_input_add("email", $email);

    $emailError = validate_email($email);
    if ($emailError !== null) validation_errors_add("email", $emailError);

    if (!validation_errors_is_empty()) redirect_and_kill("forgot-password.php");

    $uuid = uniqid("pwd_reset_", true);

    db_transaction(function (mysqli $db) use ($email, $uuid) {
        $user = db_query_row($db, "SELECT id FROM users WHERE email = ?", [$email]);

        if ($user === null) {
            validation_errors_add("email", "Użytkownik o takim emailu nie istnieje.");
            redirect_and_kill("forgot-password.php");
        }

        $alreadyActiveResets = db_query_rows($db, "SELECT * FROM password_resets WHERE user_id = ? AND created_at_timestamp > ?", [
            $user['id'],
            time() - 60 * 60
        ]);

        if (count($alreadyActiveResets) > 0) {
            validation_errors_add("email", "Na ten adres email została już wysłana prośba o zmianę hasła.");
            redirect_and_kill("forgot-password.php");
        }

        db_execute_stmt($db, 'INSERT INTO password_resets (uuid, user_id, created_at_timestamp) VALUES (?, ?, ?)', [
            $uuid, $user['id'], time()
        ]);
    });

    $subject = "Przypomnij hasło";
    $url = config("app.url") . "/auth/new-password.php?" . http_build_query([
            'uuid' => base64_encode($uuid)
        ]);
    $html = sprintf("<a href=\"%s\">%s</a>", $url, "Przypomnij hasło");

    sendMail($email, $subject, $html);

    session_flash('after_forgot_password', true);

    redirect_and_kill(config("app.url") . "/auth/after-forgot-password.php");
}

echo render_in_layout(function () { ?>
    <script>
        window.addEventListener("DOMContentLoaded", () => {
            document.querySelector("#forgot-password-form").addEventListener("submit", (e) => {
                const button = document.getElementById("reset-password-button");
                button.disabled = true;
                button.innerText = "Proszę czekać...";
            });
        });
    </script>

    <div class="flex justify-center items-center p-4">
        <form method="POST" action="/auth/forgot-password.php" id="forgot-password-form"
              class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Zapomniałeś hasła?</h1>

            <div class="flex flex-col gap-4">
                <?= render_textfield(
                    label: "Email",
                    name: "email",
                    type: 'text'
                ) ?>
            </div>

            <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg disabled:bg-blue-400 duration-300"
                        id="reset-password-button">
                    Przypomnij hasło
                </button>
                <a class="text-center w-full text-blue-200 sm:w-auto"
                   href="<?= config("app.url") . "/auth/login.php" ?>">
                    Wróć do logowania
                </a>
            </div>
        </form>
    </div>
<?php });
