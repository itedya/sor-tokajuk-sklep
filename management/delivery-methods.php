<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$db = get_db_connection();
$rows = database_delivery_methods_get($db);
$db->close();

echo render_in_layout(function() use ($rows) { ?>
    <div class="container mx-auto">
        <div class="flex flex-col max-w-3xl mx-auto gap-8 p-4">
            <h2 class="text-3xl font-bold text-neutral-300 text-center">Zarządzanie sposobami dostawy</h2>
            <?= render_table(['Nazwa', 'Cena', '', ''], array_map(function ($row) {
                $deleteUrl = base_url('/management/delivery-methods/delete.php', ['id' => $row['id'], 'back_url' => $_SERVER['REQUEST_URI']]);
                $editUrl = base_url('/management/delivery-methods/edit.php', ['id' => $row['id']]);

                return [
                    ['value' => $row['name']],
                    ['value' => $row['price']],
                    ['value' => sprintf('<a href="%s" class="px-8 py-2 bg-red-600 text-neutral-200 rounded-xl font-bold">Usuń</a>', $deleteUrl), 'is_html' => true],
                    ['value' => sprintf('<a href="%s" class="px-8 py-2 bg-yellow-600 text-neutral-200 rounded-xl font-bold">Edytuj</a>', $editUrl), 'is_html' => true],
                ];
            }, $rows)) ?>

            <div class="flex flex-row w-full justify-end items-center">
                <a href="<?= base_url('/management/delivery-methods/create.php') ?>"
                    class="px-8 py-2 bg-green-600 text-neutral-200 rounded-xl font-bold">Dodaj nowy sposób dostawy</a>
            </div>
        </div>
    </div>
<?php });
