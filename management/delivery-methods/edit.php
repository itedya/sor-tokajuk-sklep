<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$backUrl = $_GET['back_url'] ?? base_url('/management/delivery-methods.php');
if (!is_array(parse_url($backUrl))) redirect_and_kill(base_url('/management/delivery-methods.php'));

if (session_has('after_delivery_method_update')) {
    ?>
        <div class="flex flex-col gap-4 justify-center items-center">
            <h2 class="text-3xl text-center text-neutral-300 font-bold">Sukces</h2>

            <p class="text-neutral-200">Pomyślnie zapisano zmiany w sposobie dostawy.</p>

            <a href="<?= htmlspecialchars($backUrl) ?>" 
                class="px-8 py-2 text-neutral-200 bg-blue-600 rounded-xl font-bold">Wróć do listy sposobów dostawy</a>
        </div>
    <?php
    die();
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill($backUrl);
if (!is_numeric($id)) redirect_and_kill($backUrl);

$id = intval($id);

$db = get_db_connection();
$data = database_delivery_methods_get_by_id($db, $id);
if ($data === null) redirect_and_kill($backUrl);
$db->close();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    foreach ($_POST as $key => $value) old_input_add($key, $value);

    $validationErrorUrl = base_url('/management/delivery-methods/edit.php', ['id' => $id, 'render_without_layout' => 1, 'back_url' => $backUrl]);

    if (!isset($_POST['name'])) validation_errors_add("name", "Nazwa jest wymagana");
    if (!isset($_POST['price'])) validation_errors_add("price", "Cena jest wymagana");

    if (!validation_errors_is_empty()) redirect_and_kill($validationErrorUrl);

    $name = $_POST['name'];
    $name = trim($name);

    $price = $_POST['price'];

    if (!is_numeric($price)) validation_errors_add("price", "Cena musi być liczbą");

    if (!validation_errors_is_empty()) redirect_and_kill($validationErrorUrl);

    $price = intval($price);

    if (strlen($name) < 3) validation_errors_add("name", "Nazwa musi mieć więcej niż 3 znaki");
    if (strlen($name) > 64) validation_errors_add("name", "Nazwa nie może mieć więcej niż 64 znaki");
    if ($price < 0) validation_errors_add("price", "Cena nie może być mniejsza niż 0");
    if ($price > 999999999) validation_errors_add("price", "Cena nie może być większa niż 999 999 999zł.");

    if (!validation_errors_is_empty()) redirect_and_kill($validationErrorUrl);

    db_transaction(function (mysqli $db) use ($id, $name, $price) {
        database_delivery_methods_update($db, $id, $name, $price);
    });

    session_flash('after_delivery_method_update', true);
    redirect_and_kill($validationErrorUrl);
} else {
    if (!old_input_has("name")) old_input_add("name", $data['name']);
    if (!old_input_has("price")) old_input_add("price", $data['price']);
}


ob_start();
?>
<form class="flex mx-auto flex-col gap-4 justify-center items-center max-w-3xl">
    <h2 class="text-3xl text-neutral-200 font-bold">Edytuj sposób dostawy</h2>

    <?= render_textfield(label: "Nazwa", name: "name") ?>
    <?= render_textfield(label: "Cena", name: "price", type: "number") ?>
    
    <div class="flex flex-row justify-end items-center w-full gap-4">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="px-8 py-2 bg-neutral-600 text-neutral-200 font-bold rounded-xl">Wróć do poprzedniej strony</a>
        <button hx-post="<?= base_url('/management/delivery-methods/edit.php', ['id' => $id, 'back_url' => $backUrl]) ?>"
                hx-target="form"
                hx-swap="outerHTML"
                hx-include="form"
                hx-trigger="click"
                class="px-8 py-2 bg-yellow-600 text-neutral-200 font-bold rounded-xl">Zapisz</button>
    </div>
</form>
<?php
$content = ob_get_clean();

if (!isset($_GET['render_without_layout'])) {
    echo render_in_layout(function() use ($content) { ?>
        <div class="container mx-auto p-4">
            <?= $content ?>
        </div>
<?php });
} else {
    echo $content;
}
