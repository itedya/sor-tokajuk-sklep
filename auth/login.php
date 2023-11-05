<?php

require_once "../frontend-tooling/autoload.php";
require_once "../backend-tooling/autoload.php";
loadFrontendTooling();
loadBackendTooling();

AuthorizationFacade::redirectIfAuthorized();

function postMethod()
{
    OldInputFacade::clear();
    ValidationErrorFacade::clear();

    if (!isset($_POST['email'])) ValidationErrorFacade::add('email', 'Email jest wymagany!');
    if (!isset($_POST['password'])) ValidationErrorFacade::add('password', 'Hasło jest wymagane!');

    if (ValidationErrorFacade::hasErrors()) {
        return;
    }

    $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
    $password = $_POST['password'];

    OldInputFacade::add("email", $email);

    login($email, $password);
}

function login($email, $password)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("Select id, password from users where email = ?");

    /* bind parameters for markers */
    $stmt->bind_param("s", $email);

    /* execute query */
    $stmt->execute();

    $id = null;
    $passwordFromDB = null;

    $stmt->bind_result($id, $passwordFromDB);

    if ($stmt->fetch()) {
        if (!password_verify($password, $passwordFromDB)) {
            ValidationErrorFacade::add('email', 'Dane do logowania nie zgadzają się');
            return;
        }

        AuthorizationFacade::authorize($id);
        header('Location: ../index.php');
    } else {
        ValidationErrorFacade::add('email', 'Dane do logowania nie zgadzają się');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    postMethod();
}

$errors = [
    'email' => ValidationErrorFacade::renderInComponent("email"),
    'password' => ValidationErrorFacade::renderInComponent("password"),
];

$oldInput = [
    'email' => OldInputFacade::get("email")
];

$body = <<<HTML
<div class="flex justify-center items-center p-4">
<form method="POST" action="/auth/login.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
    <h1 class="text-4xl font-bold text-center text-neutral-300">Logowanie</h1>

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <label for="email" class="text-lg text-neutral-300 font-semibold mx-2">Email</label>
            <input type="email" name="email" id="email" value="{$oldInput['email']}"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
                   {$errors['email']}
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Hasło</label>
            <input type="password" name="password" id="password"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
                   {$errors['password']}
        </div>
    </div>

    <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
        <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zaloguj</button>
        <a class="text-center w-full text-blue-200 sm:w-auto" href="/auth/forgot-password.php">Nie pamiętam hasła</a>
    </div>
</form>
</div>
HTML;

ValidationErrorFacade::clear();
OldInputFacade::clear();

echo (new Layout($body))->render();
?>