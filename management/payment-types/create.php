<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_get('after_payment_type_creation', false)) {
    echo render_in_layout(function () { ?>
        <div class="container mx-auto">
            <div class="flex flex-col gap-4 justify-center items-center p-4">
                <h2 class="text-3xl font-bold text-neutral-300 text-center">Sukces</h2>
                <p class="text-neutral-200 text-center">Pomyślnie dodano nowy sposób płatności</p>
                <a href="<?= base_url('/management/payment-types.php') ?>" class="px-8 py-2 bg-blue-600 text-neutral-200 rounded-xl font-bold">Powrót do listy sposobów płatności</a>
            </div>
        </div>
    <?php });
    return;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (empty($_POST['name'])) validation_errors_add("name", "Nazwa jest wymagana.");

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);

    if (gettype($_POST['name']) !== "string") validation_errors_add("name", "Nazwa musi być tekstem.");

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);

    $name = $_POST['name'];

    if (strlen($name) < 3) validation_errors_add("name", "Nazwa musi mieć więcej niż 3 znaki");
    if (strlen($name) > 64) validation_errors_add("name", "Nazwa nie może mieć więcej niż 64 znaki.");

    if (!validation_errors_is_empty()) redirect_and_kill($_SERVER['REQUEST_URI']);
    
    db_transaction(function(mysqli $db) use ($name) {
        if (database_payment_types_does_exist_by_name($db, $name)) {
            validation_errors_add("name", "Metoda płatności o tej nazwie już istnieje!");
            redirect_and_kill($_SERVER['REQUEST_URI']);
        }

        database_payment_types_create($db, $name);
    });

    session_flash('after_payment_type_creation', true);
    redirect_and_kill($_SERVER['REQUEST_URI']);
}

echo render_in_layout(function() { ?>
<div class="container mx-auto">
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" class="flex flex-col gap-4 max-w-3xl mx-auto">
        <h2 class="text-3xl font-bold text-neutral-300 text-center">Stwórz metodę płatności</h2>

        <?= render_textfield(label: "Nazwa", name: "name") ?>
        
        <div class="flex flex-row w-full justify-end">
            <button class="px-8 py-2 bg-green-600 text-neutral-200 rounded-xl font-bold">Dodaj</button>
        </div>
    </form>
</div>
<?php });
