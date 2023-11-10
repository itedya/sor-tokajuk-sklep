<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_logged_in();

if (!session_has('after_forgot_password')) {
    redirect_and_kill("../index.php");
}

session_remove('after_forgot_password');

echo render_in_layout(function () { ?>

    <div class="flex flex-col justify-center items-center p-4 gap-8">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Teraz wejdź na maila</h1>
        <p class="text-xl text-neutral-200 text-center">Na twój adres email został wysłany link z resetem hasła.</p>
    </div>

<?php });