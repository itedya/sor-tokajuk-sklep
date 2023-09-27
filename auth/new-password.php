<?php

require_once "../frontend-tooling/autoload.php";
loadFrontendTooling("..");

OldInputFacade::clear();
ValidationErrorFacade::clear();

function backend()
{
    // TODO: Knop dokończ backend pls
    die("Knop dokończ backend pls");
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    backend();
}

$errors = [
    'new_password' => ValidationErrorFacade::renderInComponent('new_password'),
    'repeat_new_password' => ValidationErrorFacade::renderInComponent('repeat_new_password')
];

$body = <<<HTML
<div class="flex justify-center items-center p-4">
    <form method="POST" action="/auth/forgot-password.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Ustaw nowe hasło</h1>
    
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Nowe hasło</label>
                <input type="password" name="password" id="password"
                       class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
                       />
                {$errors['new_password']}
            </div>
            
            <div class="flex flex-col gap-1">
                <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Powtórz nowe hasło</label>
                <input type="password" name="password" id="password"
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