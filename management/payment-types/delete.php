<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if ($_SERVER['REQUEST_METHOD'] !== "GET") abort(404);

if (session_get('after_payment_types_deletion', false)) {
    echo render_in_layout(function () { ?>
    <div class="container mx-auto">
        <div class="flex flex-col justify-center items-center gap-8">
            <h2 class="text-3xl text-neutral-300 text-center font-bold">Sukces</h2>
            <p class="text-center text-neutral-200">Pomyślnie usunięto sposób płatności.</p>
            <a href="<?= base_url('/management/payment-types.php') ?>" class="px-8 py-2 bg-blue-600 text-neutral-200 font-bold rounded-xl">Powrót do listy sposobów płatności</a>
        </div>
    </div>
    <?php });
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(base_url('/management/payment-types.php'));
if (!is_numeric($id)) redirect_and_kill(base_url('/management/payment-types.php'));

$id = intval($id);

db_transaction(function ($db) use ($id) {
    if (database_payment_types_get_by_id($db, $id) === null) {
        redirect_and_kill(base_url('/management/payment-types.php'));
    }

    database_payment_types_delete_by_id($db, $id);
});

session_flash('after_payment_types_deletion', true);
redirect_and_kill($_SERVER['REQUEST_URI']);
