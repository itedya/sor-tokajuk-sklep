<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$userId = auth_get_user_id();

$db = get_db_connection();
$user = database_users_get_by_id($db, $userId);

$orders = database_orders_get_by_user_id($db, $userId);

if ($user === null) {
    throw new Exception("User not found");
}

echo render_in_layout(function () use ($user, $orders) { ?>
    <div class="container mx-auto flex flex-col gap-8 p-4 text-neutral-200">
        <div class="flex flex-col gap-4 w-full">
            <div class="flex flex-col gap-2 text-center">
                <h1 class="text-3xl font-bold">Panel klienta</h1>
                <p class="text-xl">Witaj, <?= htmlspecialchars($user['email']) ?>!</p>
            </div>

            <a href="<?= base_url("/management/users/edit.php", ['id' => $user['id']]) ?>"
               class="px-4 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg text-center">Edytuj swoje
                dane</a>
        </div>

        <div class="flex flex-col gap-4">
            <h2 class="text-2xl font-bold">Zamówienia</h2>

            <div class="flex flex-col gap-4 w-full divide-y divide-neutral-800">
                <?php foreach ($orders as $order): ?>
                    <div class="flex flex-col gap-2 py-4">
                        <div class="flex flex-row justify-between">
                            <p class="text-xl font-bold">Zamówienie #<?= htmlspecialchars($order['id']) ?></p>
                            <p class="text-xl">
                                <?php if ($order['status'] === 0): ?>
                                    W trakcie realizacji
                                <?php elseif ($order['status'] === 1): ?>
                                    Zrealizowane
                                <?php else: ?>
                                    Nieznany status
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex">
                            <a class="w-full sm:w-auto px-8 py-2 bg-sky-600 text-neutral-200 font-semibold rounded-lg text-center"
                               href="<?= base_url('/orders/details.php', ['id' => $order['id']]) ?>">Zobacz
                                szczegóły</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php });