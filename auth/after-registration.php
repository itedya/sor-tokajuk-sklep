<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_logged_in();

if (!session_has('after_registration')) {
    redirect_and_kill("../index.php");
}

session_remove('after_registration');

echo render_in_layout(function () { ?>
    <div class="flex flex-col justify-center items-center p-4 gap-8">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Potwierdź swojego maila</h1>
        <p class="text-xl text-neutral-200 text-center">Na twój adres email został wysłany link aktywacyjny.</p>
        <a class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg disabled:bg-blue-400 duration-300" href="<?= config("app.url") . "/" ?>">Powrót do strony głównej</a>
    </div>
<?php });