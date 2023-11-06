<?php

require_once "../frontend-tooling/autoload.php";
require_once "../backend-tooling/autoload.php";
loadFrontendTooling();
loadBackendTooling();

auth_redirect_if_logged_in();

OldInputFacade::clear();
ValidationErrorFacade::clear();

function get_method_backend()
{
    $query_params = get_query_params();

    if (isset($query_params['expired']) && $query_params['expired'] === 'true') {
        http_response_code(200);
        $body = <<<HTML
<div class="flex justify-center items-center p-4">
    <div class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Nieprawidłowy link</h1>
    
        <div class="flex flex-col gap-4 text-center text-neutral-300">
            Ten link już wygasł, wygeneruj nowy.
        </div>
    </div>
</div>
HTML;

        echo (new Layout($body))->render();
        die();
    }

    if (!isset($query_params['uuid'])) {
        redirect_and_kill(config("app.url") . "/");
    }

    $uuid = base64_decode($query_params['uuid']);

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

function post_method_backend()
{
    $query_params = get_query_params();

    if (!isset($query_params['uuid'])) {
        redirect_and_kill(config("app.url") . "/");
    }

    $uuid = base64_decode($query_params['uuid']);

    if ($uuid === false) redirect_and_kill(config("app.url") . "/");

    if (!isset($_POST['new_password'])) {
        ValidationErrorFacade::add("new_password", "Nowe hasło jest wymagane.");
    }

    if (!isset($_POST['repeat_new_password'])) {
        ValidationErrorFacade::add("repeat_new_password", "Powtórzenie nowego hasła jest wymagane.");
    }

    if (ValidationErrorFacade::hasErrors()) {
        return;
    }

    if ($_POST['new_password'] !== $_POST['repeat_new_password']) {
        ValidationErrorFacade::add("new_password", "Hasła się nie zgadzają.");
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

    $body = <<<HTML
        <div class="flex justify-center items-center p-4">
            <div class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
                <h1 class="text-4xl font-bold text-center text-neutral-300">Sukces</h1>
            
                <div class="flex flex-col gap-4 text-center text-neutral-300">
                    Twoje hasło zostało zresetowane, możesz już się zalogować.
                    
                    <div class="flex flex-col items-center justify-center gap-4">
                        <a href="./login.php" class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zaloguj się</a>
                    </div>
                </div>
            </div>
        </div>
HTML;

    echo (new Layout($body))->render();
    die();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") post_method_backend();
else get_method_backend();

$query_params = get_query_params();
$uuid = $query_params['uuid'];

$errors = [
    'new_password' => ValidationErrorFacade::renderInComponent('new_password'),
    'repeat_new_password' => ValidationErrorFacade::renderInComponent('repeat_new_password')
];

$body = <<<HTML
<div class="flex justify-center items-center p-4">
    <form method="POST" action="/auth/new-password.php?uuid=$uuid" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Ustaw nowe hasło</h1>
    
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label for="new_password" class="text-lg text-neutral-300 font-semibold mx-2">Nowe hasło</label>
                <input type="password" name="new_password" id="new_password"
                       class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
                       />
                {$errors['new_password']}
            </div>
            
            <div class="flex flex-col gap-1">
                <label for="repeat_new_password" class="text-lg text-neutral-300 font-semibold mx-2">Powtórz nowe hasło</label>
                <input type="password" name="repeat_new_password" id="repeat_new_password"
                       class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
                       />
                {$errors['repeat_new_password']}
            </div>
        </div>
    
        <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
            <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Ustaw nowe hasło</button>
        </div>
    </form>
</div>
HTML;


echo (new Layout($body))->render();