<?php

require_once "../frontend-tooling/autoload.php";
require_once "../backend-tooling/autoload.php";
loadFrontendTooling();
loadBackendTooling();

auth_redirect_if_logged_in();

OldInputFacade::clear();
ValidationErrorFacade::clear();

function backend(): void
{
    if (!isset($_POST['email'])) {
        ValidationErrorFacade::add("email", "Pole email jest wymagane.");
    } else {
        OldInputFacade::add("email", $_POST['email']);
    }

    if (ValidationErrorFacade::hasErrors()) return;

    $uuid = uniqid("pwd_reset_", true);
    $email = $_POST['email'];
    try {
        db_transaction(function (mysqli $db) use ($email, $uuid) {
            $user = db_query_row($db, "SELECT id FROM users WHERE email = ?", [
                $email
            ]);

            if ($user === null) {
                ValidationErrorFacade::add("email", "Użytkownik o takim emailu nie istnieje.");
                throw new InvalidArgumentException("User with this email does not exist.");
            }

            db_execute_stmt($db, 'INSERT INTO password_resets (uuid, user_id, created_at_timestamp) VALUES (?, ?, ?)', [
                $uuid,
                $user['id'],
                time()
            ]);
        });

        $email = $_POST['email'];
        $subject = "Przypomnij hasło";
        $url = config("app.url") . "/auth/new-password.php?" . http_build_query([
                'uuid' => base64_encode($uuid)
            ]);
        $html = sprintf("<a href=\"%s\">%s</a>", $url, "Przypomnij hasło");

        sendMail($email, $subject, $html);
    } catch (Exception $e) {
        if ($e instanceof InvalidArgumentException && $e->getMessage() === "User with this email does not exist.") {
            return;
        }

        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    backend();
}

$oldInput = [
    'email' => OldInputFacade::get('email')
];

$errors = [
    'email' => ValidationErrorFacade::renderInComponent('email')
];

$body = <<<HTML
    <div class="flex justify-center items-center p-4">
<form method="POST" action="/auth/forgot-password.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
    <h1 class="text-4xl font-bold text-center text-neutral-300">Zapomniałeś hasła?</h1>

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <label for="email" class="text-lg text-neutral-300 font-semibold mx-2">Email</label>
            <input type="email" name="email" id="email"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
                   value="{$oldInput['email']}"
                   />
            {$errors['email']}
        </div>
    </div>

    <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
        <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Przypomnij hasło</button>
        <a class="text-center w-full text-blue-200 sm:w-auto" href="/auth/forgot-password.php">Wróć do logowania</a>
    </div>
</form>
</div>
HTML;


echo (new Layout($body))->render();