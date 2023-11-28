<?php

require_once __DIR__ . '/tooling/autoload.php';

$id = $_GET['id'] ?? null;
if ($id === null) abort(404);

$db = get_db_connection();
$product = database_products_get_by_id($db, $id);
if ($product === null) {
    $db->close();
    abort(404);
}

$images = database_products_images_get_by_product_id($db, $product['id']);
$parameters = database_parameters_get_by_product_id($db, $product['id']);
$category = database_categories_get_by_id($db, $product['category_id']);

$db->close();

$parameters[] = ['name' => 'Kategoria', 'value' => $category['name']];

echo render_in_layout(function () use ($product, $images, $parameters) { ?>
    <style type="text/tailwindcss">
        <?= file_get_contents(__DIR__ . '/assets/css/product-card.css') ?>
    </style>

    <div class="container mx-auto p-4 flex flex-col gap-4 max-w-4xl">
        <div class="item">
            <div class="item-img"
                 style="background-image: url('<?= base_url("/images/" . $images[0]['image']) ?>');">
            </div>

            <h2 class="item-title"><?= htmlspecialchars($product['name']) ?></h2>
            <p class="item-description"><?= htmlspecialchars($product['description']) ?></p>
            <span class="item-price"><?= htmlspecialchars($product['price']) ?>zł</span>

            <div class="item-buttons">
                <form hx-post="<?= base_url('/product.php', ['action' => 'add-to-favourites', 'id' => $product['id']]) ?>"
                      hx-trigger="submit" hx-swap="outerHTML">
                    <button class="px-4 py-2 bg-yellow-600 text-neutral-200 rounded-xl font-bold">
                        Dodaj do ulubionych
                    </button>
                </form>
                <form hx-post="<?= base_url('/product.php', ['action' => 'add-to-basket', 'id' => $product['id']]) ?>"
                      hx-trigger="submit" hx-swap="outerHTML">
                    <button class="px-4 py-2 bg-green-600 text-neutral-200 rounded-xl font-bold">
                        Dodaj do koszyka
                    </button>
                </form>
            </div>
        </div>

        <?= render_column_table(array_map(function ($row) {
            return [
                ['type' => 'COLUMN', 'value' => $row['name']],
                ['type' => 'VALUE', 'value' => $row['value']]
            ];
        }, $parameters)) ?>
    </div>
<?php });