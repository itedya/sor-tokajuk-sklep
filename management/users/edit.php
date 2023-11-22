<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // ...
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(base_url("/management/users.php"));

$users = ['id' => $id];

echo render_in_layout(function () use ($users) { ?>
    <div class="container mx-auto p-4 gap-8 flex flex-col">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Dodaj użytkownika
        </div>

        <form action="<?= base_url("/management/users/edit.php") ?>" method="POST"
              class="flex flex-col gap-4 mx-auto w-full md:w-1/2">
            <input type="hidden" name="id" value="<?= $users['id'] ?>"/>

            <?= render_textfield(
                label: "Email",
                name: "email",
                type: "email"
            ) ?>

            <?= render_textfield(
                label: "Hasło",
                name: "password",
                type: "password"
            ) ?>

            <?= render_select(
                label: "Rola",
                name: "is_admin",
                options: [
                    ['value' => 0, 'text' => 'Klient'],
                    ['value' => 1, 'text' => 'Pracownik'],
                ]
            ) ?>

            <div class="flex flex-row justify-end items-center gap-4">
                <a href="<?= config("app.url") . "/management/users.php" ?>"
                   class="px-8 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do użytkowników
                </a>

                <button type="submit" class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg">
                    Edytuj
                </button>
            </div>
        </form>
    </div>
<?php });