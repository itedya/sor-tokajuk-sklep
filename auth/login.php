<?php
require_once "../frontend-tooling/autoload.php";
loadFrontendTooling("..");

$errors = [];

function postMethod() {
    if (!isset($_POST['email'])) {
        $errors['email'] = 'Email jest wymagany!';
        return;
    }
    if (!isset($_POST['password'])) {
        $errors['password'] = 'Hasło jest wymagane!';
        return;
    }
    $password = $_POST['password'];
    $email = $_POST['email'];   
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    postMethod();
}


$body = <<<HTML
<div class="flex justify-center items-center p-4">
<form method="POST" action="/auth/login.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
    <h1 class="text-4xl font-bold text-center text-neutral-300">Logowanie</h1>

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <label for="email" class="text-lg text-neutral-300 font-semibold mx-2">Email</label>
            <input type="email" name="email" id="email"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Hasło</label>
            <input type="password" name="password" id="password"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
        <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zaloguj</button>
        <a class="text-center w-full text-blue-200 sm:w-auto" href="/auth/forgot-password.php">Nie pamiętam hasła</a>
    </div>
</form>
</div>
HTML;

echo (new Layout($body))->render();
?>
