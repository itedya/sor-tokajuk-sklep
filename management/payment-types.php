<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$db = get_db_connection();
$rows = database_payment_types_get($db);
$db->close();

echo render_in_layout(function() use ($rows) { ?>
<div class="container mx-auto p-4">
    <div class="flex flex-col gap-8 justify-center max-w-3xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-neutral-300">Zarządzaj metodami płatności</h2>

        <div class="flex flex-col gap-4">
            <?= render_table(['Nazwa', ''], array_map(function ($row) {
                $deleteUrl = base_url('management/payment-types/delete.php', ['id' => $row['id']]);

                return [
                    ['value' => $row['name']],
                    ['value' => '<a href="' . $deleteUrl . '" class="px-4 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg text-center">Usuń</a>', 'is_html' => true]
                ];
            }, $rows)); ?>
        </div>

        <div class="flex flex-row justify-end items-center">
        <a class="px-4 py-2 bg-green-600 text-neutral-200 font-semibold rounded-lg text-center"
           href="<?= base_url('/management/payment-types/create.php') ?>">Dodaj nową metodę</a>
        </div>
    </div>
</div>
<?php });
