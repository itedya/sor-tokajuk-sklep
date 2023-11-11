<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
// TODO: Kuba zabezpiecz - jeżeli nie jest adminem, to go przekierowuje do strony głównej

$products = [
    [
        'id' => 1,
        'name' => 'Trumna drewniana',
        'description' => 'Trumna wykonana z drewna, elegancka i solidna.',
        'price' => 2000.00,
    ],
    [
        'id' => 2,
        'name' => 'Trumna metalowa',
        'description' => 'Trumna wykonana z metalu, trwała i nowoczesna.',
        'price' => 2500.00,
    ],
    [
        'id' => 3,
        'name' => 'Trumna dziecięca',
        'description' => 'Specjalnie zaprojektowana trumna dla dzieci.',
        'price' => 1500.00,
    ],
    [
        'id' => 4,
        'name' => 'Trumna ekologiczna',
        'description' => 'Trumna wykonana z materiałów ekologicznych.',
        'price' => 1800.00,
    ],
    [
        'id' => 2,
        'name' => 'Usługa pogrzebowa',
        'description' => 'Pełen zakres usług związanych z organizacją pogrzebu.',
        'price' => 5000.00,
    ],
    [
        'id' => 3,
        'name' => 'Kwiaty pogrzebowe',
        'description' => 'Bukiety kwiatów dedykowane na ceremonię pogrzebową.',
        'price' => 300.00,
    ]
]; // TODO: Kuba wyselectuj wszystko z tabeli products

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

    <div class="container mx-auto p-4">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Produkty
        </div>

        <div class="grid grid-cols-1 auto-grid-rows gap-8 xl:grid-cols-2">
            <?php foreach ($products as $product): ?>
                <div class="item">
                    <div class="item-img" style="background-image: url('https://placehold.co/400x400');"></div>

                    <h2 class="item-title"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="item-description"><?= htmlspecialchars($product['description']) ?></p>
                    <span class="item-price"><?= htmlspecialchars($product['price']) ?>zł</span>

                    <div class="item-buttons flex flex-row justify-end items-center gap-4">
                        <a class="px-8 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg">
                            Usuń
                        </a>
                        <a class="px-8 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg">
                            Edytuj
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php });

