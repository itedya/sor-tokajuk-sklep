<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$db = get_db_connection();

$orders = database_orders_get($db);

$db->close();

echo render_in_layout(function () use ($orders) { ?>
    <div class="w-full max-w-4xl mx-auto flex flex-col gap-4">
        <h2 class="text-3xl font-bold text-left text-neutral-200">Zamówienia</h2>
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
                    ['value' => sprintf(
                        '<a href="%s" class="px-4 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-lg text-center">Szczegóły</a>',
                        base_url("/orders/details.php", [
                            'id' => $row['id'],
                            'previous_page' => $_SERVER['REQUEST_URI']
                        ])),
                        'is_html' => true
                    ]
                ];
            }, $orders)
        ) ?>
    </div>
<?php });