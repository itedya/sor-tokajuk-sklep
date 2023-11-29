<?php

require_once __DIR__ . '/../tooling/autoload.php';

$orderId = $_GET['id'] ?? null;
if (!is_numeric($orderId)) abort(404);
$orderId = intval($orderId);

$db = get_db_connection();
$order = database_orders_get_by_id($db, $_GET['id']);

if ($order === null) abort(404);

$user = null;
if ($order['user_id'] !== null) {
    gate_redirect_if_unauthorized();
    $user = database_users_get_by_id($db, $order['user_id']);
}

if ($order['user_id'] !== auth_get_user_id() && !auth_is_admin()) {
    abort(404);
}

$address = database_addresses_get_by_id_with_deleted($db, $order['address_id']);

$deliveryAddress = database_addresses_get_by_id_with_deleted($db, $order['delivery_address_id']);

$deliveryMethod = database_delivery_method_get_by_id_with_deleted($db, $order['delivery_method_id']);

$products = database_products_get_by_order_id($db, $orderId);

$payment = database_payment_type_get_by_id_with_deleted($db, $order['payment_type_id']);

echo render_in_layout(function () use ($order, $user, $address, $products, $deliveryMethod, $payment, $deliveryAddress) { ?>
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
                    ['type' => 'ROW', 'value' => $user['email'] ?? 'Użytkownik niezalogowany']
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Adres dostawy'],
                    ['type' => 'ROW', 'value' => '']
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Adres'],
                    ['type' => 'ROW', 'value' => $deliveryAddress['first_line'] . ' ' . $deliveryAddress['second_line']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Miasto'],
                    ['type' => 'ROW', 'value' => $deliveryAddress['city']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Kod pocztowy'],
                    ['type' => 'ROW', 'value' => $deliveryAddress['postal_code']]
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Adres rozliczeniowy'],
                    ['type' => 'ROW', 'value' => ''],
                ],
                [
                    ['type' => 'COLUMN', 'value' => 'Adres'],
                    ['type' => 'ROW', 'value' => $address['first_line'] . ' ' . $address['second_line']]
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
                <p class="text-xl font-bold"><?= $deliveryMethod['name'] ?></p>
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

        <div class="flex flex-row justify-end gap-2 lg:col-span-2">
            <?php if (auth_is_admin()): ?>
                <a class="px-4 py-2 border-2 border-yellow-600 text-neutral-200 hover:bg-yellow-600 duration-200 font-semibold rounded-lg text-center"
                   href="<?= base_url('/orders/change-status.php', ['id' => $order['id'], 'previous_page' => $_SERVER['REQUEST_URI']]) ?>">
                    Zmień status zamówienia
                </a>
            <?php endif; ?>
            <a class="px-4 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-lg text-center"
               href="<?= htmlspecialchars($_GET['previous_page'] ?? base_url('/client-panel/index.php')) ?>">
                Wróć do poprzedniej strony
            </a>
        </div>
    </div>
<?php });
