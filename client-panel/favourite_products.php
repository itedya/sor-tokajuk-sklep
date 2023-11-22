<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_GET['id'] ?? null;

    if ($id === null) {
        redirect_and_kill($_SERVER['REQUEST_URI']);
    }

    $db = get_db_connection();

    database_favourite_products_delete_by_user_id_and_product_id($db, auth_get_user_id(), $id);

    redirect_and_kill($_SERVER['REQUEST_URI']);
}

$db = get_db_connection();

$products = database_favourite_products_get_by_user_id_with_image($db, auth_get_user_id());

ob_start(); ?>
    <style type="text/tailwindcss">
        .item {
            display: grid;
            grid-template-columns: auto auto;
            grid-template-rows: auto auto auto auto;
            gap: 8px;
        }

        .item-img {
            grid-column: 1 / 3;
            border-radius: 20px;
            aspect-ratio: 1/1;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .item-title {
            @apply text-3xl text-neutral-300;
            grid-row: 2 / 3;
            padding: 4px;
        }

        .item-description {
            @apply text-xl text-neutral-200;
            grid-row: 3/4;
            grid-column: 1 / 3;
            padding: 4px;
        }

        .item-price {
            @apply text-xl text-neutral-200 font-bold;
            grid-row: 2/3;
            justify-self: end;
            padding: 4px;
        }

        .item-buttons {
            grid-column: 1/3;
        }

        @media (min-width: 768px) {
            .item {
                grid-template-columns: 200px auto auto;
                grid-template-rows: auto auto;
            }

            .item-img {
                grid-column: 1 / 2;
                grid-row: 1 / 3;
            }

            .item-title {
                grid-column: 2 / 3;
                grid-row: 1 / 2;
            }

            .item-description {
                grid-column: 2 / 3;
                grid-row: 2 / 3;
            }

            .item-price {
                grid-column: 3 / 4;
                grid-row: 1 / 2;
            }

            .item-buttons {
                grid-column: 2 / 4;
                grid-row: 3 / 4;
            }
        }

        @media (min-width: 1080px) {
            .item {
                grid-template-columns: 200px auto auto;
                grid-template-rows: auto auto;
            }

            .item-img {
                grid-column: 1 / 2;
                grid-row: 1 / 3;
            }

            .item-title {
                grid-column: 2 / 3;
                grid-row: 1 / 2;
            }

            .item-description {
                grid-column: 2 / 3;
                grid-row: 2 / 3;
            }

            .item-price {
                grid-column: 3 / 4;
                grid-row: 1 / 2;
            }

            .item-buttons {
                grid-column: 2 / 4;
                grid-row: 3 / 4;
            }

        }
    </style>

    <div class="w-full flex flex-col gap-4">
        <?php if (count($products) == 0): ?>
            <div class="text-3xl text-center text-neutral-300 p-4">
                Nie masz żadnych ulubionych produktów.
            </div>
        <?php endif; ?>

        <?php foreach ($products as $product): ?>
            <div class="item">
                <div class="item-img"
                     style="background-image: url('<?= base_url("/images/" . $product['image']) ?>');">
                </div>

                <h2 class="item-title"><?= htmlspecialchars($product['name']) ?></h2>
                <p class="item-description"><?= htmlspecialchars($product['description']) ?></p>
                <span class="item-price"><?= htmlspecialchars($product['price']) ?>zł</span>

                <div class="item-buttons flex flex-row justify-end items-center gap-4">
                    <button hx-post="<?= base_url('/client-panel/favourite_products.php', ['id' => $product['id']]) ?>"
                            hx-trigger="click"
                            hx-swap="innerHTML"
                            hx-target="#swappable-panel"
                            class="px-8 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg">
                        Usuń z ulubionych
                    </button>
                    <a class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg"
                       href="<?= base_url('/products/show.php', ['id' => $product['id']]) ?>">
                        Zobacz
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php
echo ob_get_clean();