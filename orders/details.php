<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

$orderId = $_GET['id'] ?? null;
if (!is_numeric($orderId)) abort(404);
$orderId = intval($orderId);

$db = get_db_connection();
$order = database_orders_get_by_id($db, $_GET['id']);

if ($order['user_id'] !== auth_get_user_id() && !auth_is_admin()) {
    abort(404);
}

$user = database_users_get_by_id($db, $order['user_id']);

$address = [
    'first_line' => 'ul. Wąwozowa 41b/22',
    'city' => 'Wąwóz',
    'postal_code' => '09-444'
];

$deliveryMethod = [
    'id' => 1,
    'name' => 'Kurier',
    'price' => 50.00
];

$products = [
    [
        'id' => 1,
        'name' => 'Trumna do gier',
        'price' => 99.99,
        'quantity' => 1
    ],
    [
        'id' => 2,
        'name' => 'Kwiaty',
        'price' => 199.99,
        'quantity' => 3
    ]
];

//$payment = null;
$payment = [
    'id' => 1,
    'name' => 'PayMedia'
];

echo render_in_layout(function () use ($order, $user, $address, $products, $deliveryMethod, $payment) { ?>
    <div class="container mx-auto flex flex-col gap-8 text-neutral-200 p-4 lg:grid lg:grid-cols-2 lg:auto-grid-rows">
        <div class="flex flex-col gap-2 text-center lg:col-span-2">
            <h2 class="text-3xl font-bold">Szczegóły zamówienia</h2>
            <p class="font-bold">Zamówienie #<?= $order['id'] ?></p>
        </div>

        <div class="flex flex-col gap-2 overflow-x-auto w-full">
            <?php
            $paymentMethod = $payment === null ? '<span class="text-red-400">Nie opłacono</span>' : $payment['name'];

            $orderStatus = match ($order['status']) {
                0 => '<span class="text-neutral-400">W trakcie realizacji</span>',
                1 => '<span class="text-green-400">Zrealizowane</span>',
                default => '<span class="text-neutral-400">Nieznany status</span>'
            };

            ?>

            <?= render_column_table([
                [
                    ['type' => 'COLUMN', 'value' => 'Zamówione przez'],
                    ['type' => 'ROW', 'value' => $user['email']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Adres'],
                    ['type' => 'ROW', 'value' => $address['first_line']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Miasto'],
                    ['type' => 'ROW', 'value' => $address['city']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Kod pocztowy'],
                    ['type' => 'ROW', 'value' => $address['postal_code']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Rodzaj płatności'],
                    ['type' => 'ROW', 'value' => $paymentMethod, 'is_html' => true]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Status zamówienia'],
                    ['type' => 'ROW', 'value' => $orderStatus, 'is_html' => true],
                ]
            ]) ?>
        </div>

        <div class="flex flex-col gap-2 divide-y divide-neutral-600">
            <div class="flex flex-col gap-2 divide-y divide-neutral-800">
                <?php foreach ($products as $product): ?>
                    <div class="flex flex-row justify-between">
                        <p class="text-xl font-bold"><?= htmlspecialchars($product['name']) ?></p>
                        <p class="text-xl"><?= htmlspecialchars($product['price'] * $product['quantity']) ?> zł</p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex flex-row justify-between">
                <p class="text-xl font-bold">Kurier</p>
                <p class="text-xl"><?= $deliveryMethod['price'] ?> zł</p>
            </div>

            <div class="flex flex-row justify-between">
                <p class="text-xl font-bold">Suma</p>
                <p class="text-xl">
                    <?= htmlspecialchars(array_reduce($products, function ($acc, $product) {
                            return $acc + $product['price'] * $product['quantity'];
                        }, 0) + $deliveryMethod['price']) ?> zł
                </p>
            </div>
        </div>
    </div>
<?php });