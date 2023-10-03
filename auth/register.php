<?php

require_once "../frontend-tooling/autoload.php";
require_once "../backend-tooling/autoload.php";
loadFrontendTooling();
loadBackendTooling();

AuthorizationFacade::redirectIfAuthorized();

function random_str(
    int    $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string
{
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces [] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function register()
{
    if (!isset($_POST['email'])) ValidationErrorFacade::add("email", "Email jest wymagany");
    else OldInputFacade::add("email", $_POST['email']);

    if (!isset($_POST['password'])) ValidationErrorFacade::add("password", "Hasło jest wymagane");
    if (!isset($_POST['repeat_password'])) ValidationErrorFacade::add("repeat_password", "Powtórzenie hasła jest wymagane");

    if (ValidationErrorFacade::hasErrors()) return;

    $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ValidationErrorFacade::add("email", "Email jest niepoprawny");
        return;
    }

    if (strlen($password) < 8) {
        ValidationErrorFacade::add("password", "Hasło musi mieć co najmniej 8 znaków");
        return;
    }
    if ($password !== $repeat_password) {
        ValidationErrorFacade::add("repeat_password", "Hasła muszą być takie same");
        return;
    }

    $conn = getDatabaseConnection();

    // Check if user with this email already exists

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $numRows = $stmt->get_result()->num_rows;

    $stmt->close();
    if ($numRows > 0) {
        ValidationErrorFacade::add("email", "Użytkownik o tym adresie email już istnieje");
        return;
    }

    // Hash the password

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into database

    $stmt = $conn->prepare("INSERT INTO users (email, `password`) VALUES (?, ?);");
    $stmt->bind_param("ss", $email, $passwordHash);
    $stmt->execute();

    $id = $stmt->insert_id;

    $stmt->close();

    $hash = random_str();

    $stmt = $conn->prepare("INSERT INTO email_verification_attempts (`user_id`, `hash`) VALUES (?, ?);");
    $stmt->bind_param("is", $id, $hash);
    $stmt->execute();

    $stmt->close();

    sendMail($email, 'Potwierdź email do konta', '<a href="http://localhost/auth/confirm-email.php?hash=' . $hash . '">Kliknij tutaj aby potwierdzić hasło</a>');

    AuthorizationFacade::authorize($id);
    header('Location: ../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    register();
}

$errors = [
    'email' => ValidationErrorFacade::renderInComponent("email"),
    'password' => ValidationErrorFacade::renderInComponent("password"),
    'repeat_password' => ValidationErrorFacade::renderInComponent("repeat_password"),
];

$oldInput = [
    'email' => OldInputFacade::get("email")
];

$body = <<<HTML
<div class="flex justify-center items-center p-4">
<form method="POST" action="/auth/register.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
    <h1 class="text-4xl font-bold text-center text-neutral-300">Rejestracja</h1>

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <label for="email" class="text-lg text-neutral-300 font-semibold mx-2">Email</label>
            <input type="email" name="email" id="email"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
                   value="{$oldInput['email']}"
                   />
            {$errors['email']}
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Hasło</label>
            <input type="password" name="password" id="password"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
            {$errors['password']}
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Powtórz hasło</label>
            <input type="password" name="repeat_password" id="repeat_password"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
            {$errors['repeat_password']}
        </div>
    </div>

    <div class="flex justify-end">
        <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zarejestruj się</button>
    </div>
</form>
</div>
HTML;

ValidationErrorFacade::clear();
OldInputFacade::clear();

echo (new Layout($body))->render();
?>
