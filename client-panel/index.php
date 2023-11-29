<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$userId = auth_get_user_id();

$db = get_db_connection();
$user = database_users_get_by_id($db, $userId);

echo render_in_layout(function () use ($user) { ?>
    <div class="container mx-auto flex flex-col gap-8 p-4 text-neutral-200 md:grid md:grid-cols-3 md:auto-grid-rows lg:grid-cols-4">
        <div class="flex flex-col gap-2 text-center w-full md:col-span-3 lg:col-span-4">
            <h1 class="text-3xl font-bold">Panel klienta</h1>
            <p class="text-xl">Witaj, <?= htmlspecialchars($user['email']) ?>!</p>
        </div>

        <div class="flex flex-col divide-y divide-neutral-700 border border-neutral-700 rounded-xl">
            <button hx-get="<?= base_url("/client-panel/edit.php") ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    class="py-4 px-6 text-neutral-300 text-left">Edytuj swoje dane
            </button>
            <button hx-get="<?= base_url("/client-panel/change_password.php") ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    class="py-4 px-6 text-neutral-300 text-left">Ustaw nowe hasło
            </button>
            <button hx-get="<?= base_url("/client-panel/favourite_products.php") ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    class="py-4 px-6 text-neutral-300 text-left">Ulubione produkty
            </button>
            <button hx-get="<?= base_url("/client-panel/orders.php") ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    class="py-4 px-6 text-neutral-300 text-left">Zamówienia
            </button>
            <button hx-get="<?= base_url("/client-panel/addresses.php") ?>"
                    hx-trigger="click"
                    hx-swap="innerHTML"
                    hx-target="#swappable-panel"
                    class="py-4 px-6 text-neutral-300 text-left">Adresy
            </button>
        </div>

        <div id="swappable-panel"
             class="md:col-span-2 md:row-span-2 lg:col-span-3 w-full justify-self-center border border-neutral-700 rounded-xl p-4 md:p-8">
            <?php
            $panel = $_GET['panel'] ?? 'edit';
            if (!in_array($panel, ['edit', 'addresses'])) {
                $panel = 'edit';
            }

            require __DIR__ . "/" . $panel . ".php"; ?>
        </div>
    </div>
<?php });
