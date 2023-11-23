<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

$id = $_GET['id'] ?? null;
if (!is_numeric($id)) abort(404);

$id = intval($id);

$db = get_db_connection();

$address = database_addresses_get_by_id($db, $id);
if ($address === null) abort(404);

if ($address['user_id'] !== auth_get_user_id() && !auth_is_admin()) {
    abort(404);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ...
}

if (!old_input_has('first_line')) old_input_add('first_line', $address['first_line']);
if (!old_input_has('city')) old_input_add('city', $address['city']);
if (!old_input_has('postal_code')) old_input_add('postal_code', $address['postal_code']);

ob_start(); ?>
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-2 text-center lg:col-span-2">
            <h2 class="text-3xl font-bold">Edytuj adres</h2>
        </div>

        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" class="flex flex-col gap-4 overflow-x-auto w-full">
            <?= render_textfield(
                label: 'Adres',
                name: 'first_line'
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
                    class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-xl">Zapisz
            </button>
        </div>
    </div>
<?php
echo ob_get_clean();