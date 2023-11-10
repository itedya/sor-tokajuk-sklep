<?php

require_once '../tooling/autoload.php';

gate_redirect_if_logged_in();

function backend(): void
{
    if (!isset($_POST['email'])) {
        validation_errors_add("email", "Pole email jest wymagane.");
    } else {
        old_input_add("email", $_POST['email']);
    }

    if (!validation_errors_is_empty()) return;

    $uuid = uniqid("pwd_reset_", true);
    $email = $_POST['email'];

    db_transaction(function (mysqli $db) use ($email, $uuid) {
        $user = db_query_row($db, "SELECT id FROM users WHERE email = ?", [$email]);

        if ($user === null) {
            validation_errors_add("email", "Użytkownik o takim emailu nie istnieje.");
            redirect_and_kill("forgot-password.php");
        }

        db_execute_stmt($db, 'INSERT INTO password_resets (uuid, user_id, created_at_timestamp) VALUES (?, ?, ?)', [
            $uuid, $user['id'], time()
        ]);
    });

    $email = $_POST['email'];
    $subject = "Przypomnij hasło";
    $url = config("app.url") . "/auth/new-password.php?" . http_build_query([
            'uuid' => base64_encode($uuid)
        ]);
    $html = sprintf("<a href=\"%s\">%s</a>", $url, "Przypomnij hasło");

    sendMail($email, $subject, $html);
}

if ($_SERVER['REQUEST_METHOD'] === "POST") backend();

echo render_in_layout(function () { ?>
    <div class="flex justify-center items-center p-4">
        <form method="POST" action="/auth/forgot-password.php"
              class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Zapomniałeś hasła?</h1>

            <div class="flex flex-col gap-4">
                <?= render_textfield(
                    label: "Email",
                    name: "email",
                    type: 'email'
                ) ?>
            </div>

            <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Przypomnij hasło
                </button>
                <a class="text-center w-full text-blue-200 sm:w-auto" href="/auth/forgot-password.php">Wróć do
                    logowania</a>
            </div>
        </form>
    </div>
<?php });
