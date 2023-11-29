<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$sql = <<<SQL
SELECT products.*, pi.image FROM products
LEFT JOIN (
    SELECT id, product_id, image
    FROM products_images pi1
    WHERE id = (
        SELECT id
        FROM products_images pi2
        WHERE pi1.product_id = pi2.product_id
        ORDER BY pi2.id
        LIMIT 1
    )
) pi ON pi.product_id = products.id WHERE deleted_at IS NULL
SQL;

$products = db_query_rows(get_db_connection(), $sql, []);

echo render_in_layout(function () use ($products) { ?>
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
            display: flex;
            flex-direction: row;
            gap: 8px;
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

    <div class="container mx-auto p-4 gap-8 flex flex-col">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Produkty
        </div>

        <div class="flex flex-row justify-end items-center gap-4">
            <a href="<?= config("app.url") . "/management/products/create.php" ?>"
               class="px-8 py-2 bg-green-600 text-neutral-200 font-semibold rounded-lg">
                Dodaj
            </a>
        </div>

        <div class="grid grid-cols-1 auto-grid-rows gap-8 xl:grid-cols-2">
            <?php foreach ($products as $product): ?>
                <div class="item">
                    <div class="item-img"
                         style="background-image: url('<?= config("app.url") . "/images/" . $product['image'] ?>');">
                    </div>

                    <h2 class="item-title"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="item-description"><?= htmlspecialchars($product['description']) ?></p>
                    <span class="item-price"><?= htmlspecialchars($product['price']) ?>zł</span>

                    <div class="item-buttons">
                        <a href="<?= config("app.url") . "/management/products/delete.php?id=" . $product['id'] ?>"
                           class="px-8 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg">
                            Usuń
                        </a>
                        <a class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg"
                           href="<?= config("app.url") . "/management/products/edit.php?id=" . $product['id'] ?>">
                            Edytuj
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php });