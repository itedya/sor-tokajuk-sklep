<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

$db = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) old_input_add($key, $value);

    $firstLine = $_POST['first_line'] ?? null;
    $secondLine = $_POST['second_line'] ?? null;
    $city = $_POST['city'] ?? null;
    $postalCode = $_POST['postal_code'] ?? null;

    if ($firstLine === null) validation_errors_add('first_line', 'Pole jest wymagane');
    if ($secondLine === null) validation_errors_add('second_line', 'Pole jest wymagane');
    if ($city === null) validation_errors_add('city', 'Pole jest wymagane');
    if ($postalCode === null) validation_errors_add('postal_code', 'Pole jest wymagane');

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);

    $firstLine = trim($firstLine);
    $secondLine = trim($secondLine);
    $city = trim($city);
    $postalCode = trim($postalCode);

    if (strlen($firstLine) > 255) validation_errors_add('first_line', 'Pole może zawierać maksymalnie 255 znaków');
    if (strlen($secondLine) > 255) validation_errors_add('second_line', 'Pole może zawierać maksymalnie 255 znaków');
    if (strlen($city) > 255) validation_errors_add('city', 'Pole może zawierać maksymalnie 255 znaków');
    if (strlen($postalCode) > 255) validation_errors_add('postal_code', 'Pole może zawierać maksymalnie 255 znaków');

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);

    if (strlen($firstLine) < 3) validation_errors_add('first_line', 'Pole musi zawierać minimum 3 znaki');
    if (strlen($city) < 3) validation_errors_add('city', 'Pole musi zawierać minimum 3 znaki');
    if (strlen($postalCode) < 3) validation_errors_add('postal_code', 'Pole musi zawierać minimum 3 znaki');

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);

    database_addresses_create($db, auth_get_user_id(), $firstLine, $secondLine, $city, $postalCode);

    redirect_and_kill(base_url("/client-panel/addresses.php"));
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