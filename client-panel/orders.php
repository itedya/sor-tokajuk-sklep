<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

$db = get_db_connection();

$orders = database_orders_get_by_user_id($db, auth_get_user_id());

ob_start(); ?>
<div class="flex flex-col gap-4 justify-center">
    <h2 class="text-3xl font-bold text-center">Zamówienia</h2>
    <?= render_table(
        ["Identyfikator", "Status", "Akcje"],
        array_map(function ($row) {
            $statusHtml = match ($row['status']) {
                0 => '<span class="text-neutral-400">W trakcie realizacji</span>',
                1 => '<span class="text-green-400">Zrealizowane</span>',
                default => '<span class="text-neutral-400">Nieznany status</span>'
            };

            return [
                ['value' => "#" . $row['id']],
                ['value' => $statusHtml, 'is_html' => true],
                ['value' => '<a href="' . base_url("/orders/details.php", [
                        'id' => $row['id']
                    ]) . '" class="px-4 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-lg text-center">Szczegóły</a>', 'is_html' => true]
            ];
        }, $orders)
    ) ?>
</div>
<?php
echo ob_get_clean();
