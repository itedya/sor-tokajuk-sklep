<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

$db = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ...
}

ob_start(); ?>
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-2 text-center lg:col-span-2">
            <h2 class="text-3xl font-bold">Dodaj adres</h2>
        </div>

        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" class="flex flex-col gap-4 overflow-x-auto w-full">
            <?= render_textfield(
                label: 'Pierwsza linia adresu',
                name: 'first_line'
            ) ?>

            <?= render_textfield(
                label: 'Druga linia adresu',
                name: 'second_line'
            ) ?>

            <?= render_textfield(
                label: 'Miasto',
                name: 'city'
            ) ?>

            <?= render_textfield(
                label: 'Kod pocztowy',
                name: 'postal_code'
            ) ?>
        </form>

        <div class="flex flex-row justify-end gap-4">
            <button hx-get="<?= htmlspecialchars(base_url("/client-panel/addresses.php")) ?>"
                    hx-trigger="click" hx-swap="innerHTML" hx-target="#swappable-panel"
                    class="px-8 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-xl">Wróć do adresów
            </button>
            <button hx-post="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>"
                    hx-trigger="click" hx-swap="innerHTML" hx-include="form" hx-target="#swappable-panel"
                    class="px-8 py-2 bg-green-600 text-neutral-200 font-semibold rounded-xl">Zapisz
            </button>
        </div>
    </div>
<?php
echo ob_get_clean();