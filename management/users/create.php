<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_has("user_created")) {
    echo render_in_layout(function () { ?>
        <div class="container p-4 flex flex-col gap-8 mx-auto">
            <h2 class="text-3xl text-center text-neutral-300">Pracownik został dodany.</h2>
            <div class="flex justify-center items-center">
                <a href="<?= base_url("/management/users/create.php") ?>"
                   class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do użytkowników
                </a>
            </div>
        </div>
    <?php });

    redirect_and_kill($_SERVER['REQUEST_URI']);
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    session_flash("user_created", true);
    redirect_and_kill($_SERVER['REQUEST_URI']);
}

echo render_in_layout(function () { ?>
    <div class="container mx-auto p-4 gap-8 flex flex-col">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Dodaj pracownika
        </div>

        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" class="flex flex-col gap-4 mx-auto w-full lg:w-1/2">
            <?= render_textfield(
                "Email",
                "email",
                "email"
            ) ?>

            <?= render_textfield(
                "Hasło",
                "password",
                "password"
            ) ?>

            <?= render_textfield(
                "Powtórz hasło",
                "repeat_password",
                "password"
            ) ?>

            <div class="flex flex-row justify-end items-center gap-4">
                <button class="px-8 py-2 bg-green-600 text-neutral-200 font-semibold rounded-lg">
                    Dodaj
                </button>
            </div>
        </form>
    </div>
<?php });