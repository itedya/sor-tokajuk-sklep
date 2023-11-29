<?php

require_once __DIR__ . '/tooling/autoload.php';

function render_favourites_button(array $product, bool $isInFavourites): string
{
    ob_start();
    if (!$isInFavourites): ?>
        <form hx-post="<?= base_url('/product.php', ['action' => 'add-to-favourites', 'id' => $product['id']]) ?>"
              hx-trigger="submit" hx-swap="outerHTML">
            <button class="px-4 py-2 border-2 border-yellow-600 hover:border-yellow-700 hover:bg-yellow-700 text-neutral-200 rounded-xl font-bold">
                Dodaj do ulubionych
            </button>
        </form>
    <?php else: ?>
        <form hx-post="<?= base_url('/product.php', ['action' => 'remove-from-favourites', 'id' => $product['id']]) ?>"
              hx-trigger="submit" hx-swap="outerHTML">
            <button class="px-4 py-2 border-2 border-red-600 hover:border-red-700 hover:bg-red-700 text-neutral-200 rounded-xl font-bold">
                Usuń z ulubionych
            </button>
        </form>
    <?php endif;
    return ob_get_clean();
}

function render_add_to_cart_button(array $product, bool $withAddAlert = false): string
{
    ob_start();
    ?>
    <form hx-post="<?= base_url('/product.php', ['action' => 'add-to-cart', 'id' => $product['id']]) ?>"
          hx-trigger="submit" hx-swap="outerHTML" hx-target="this">
        <button id="add-to-cart-button"
                class="border-green-600 hover:border-green-700 border-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-neutral-200 rounded-xl font-bold disabled:bg-green-800">
            Dodaj do koszyka
        </button>
    </form>

    <?php if ($withAddAlert): ?>
    <script>
        (async () => {
            fetch('<?= base_url('/product.php', ['action' => 'render-navbar', 'id' => $product['id']]) ?>')
                .then(response => response.text())
                .then(html => {
                    const navbar = document.querySelector('#navbar');
                    navbar.outerHTML = html;
                });

            const addToCartButton = document.querySelector('#add-to-cart-button');

            addToCartButton.disabled = true;
            addToCartButton.innerHTML = "Dodano do koszyka";
            await new Promise(r => setTimeout(r, 1000));
            addToCartButton.disabled = false;
            addToCartButton.innerHTML = "Dodaj do koszyka";
        })();
    </script>
<?php endif;
    return ob_get_clean();
}

function render_image_viewer(string $image): string
{
    ob_start();
    ?>
    <div class="aspect-square bg-cover rounded-xl sm:h-60 lg:h-96" id="image-viewer"
         style="background-image: url('<?= base_url("/images/" . $image) ?>');">
    </div>
    <?php
    return ob_get_clean();
}

$id = $_GET['id'] ?? null;
if ($id === null) abort(404);

$db = get_db_connection();
$product = database_products_get_by_id($db, $id);
if ($product === null) {
    $db->close();
    abort(404);
}

$isInFavourites = false;
if (auth_is_logged_in()) {
    $isInFavourites = database_favourite_products_exists_by_user_id_and_product_id($db, auth_get_user_id(), $product['id']);
}

$action = $_GET['action'] ?? null;
if ($action === 'add-to-favourites') {
    if (!auth_is_logged_in()) abort(401);
    if ($isInFavourites) abort(400);

    database_favourite_products_create($db, auth_get_user_id(), $product['id']);
    $isInFavourites = !$isInFavourites;

    echo render_favourites_button($product, $isInFavourites);
    return;
} else if ($action === 'remove-from-favourites') {
    if (!auth_is_logged_in()) abort(401);
    if (!$isInFavourites) abort(400);

    database_favourite_products_delete_by_user_id_and_product_id($db, auth_get_user_id(), $product['id']);
    $isInFavourites = !$isInFavourites;

    echo render_favourites_button($product, $isInFavourites);
    return;
} else if ($action === 'add-to-cart') {
    cart_add_product($product['id']);

    echo render_add_to_cart_button($product, true);
    return;
} else if ($action === 'change-image') {
    $image = $_GET['image'] ?? null;
    if ($image === null) abort(404);

    $images = database_products_images_get_by_product_id($db, $product['id']);
    $images = array_map(fn($i) => $i['image'], $images);
    if (!in_array($image, $images)) abort(404);

    echo render_image_viewer($image);
    return;
} else if ($action === "render-navbar") {
    echo render_navbar();
    return;
}

$images = database_products_images_get_by_product_id($db, $product['id']);
$parameters = database_parameters_get_by_product_id($db, $product['id']);
$category = database_categories_get_by_id($db, $product['category_id']);

$db->close();

$parameters[] = ['name' => 'Kategoria', 'value' => $category['name']];

echo render_in_layout(function () use ($product, $images, $parameters, $isInFavourites) { ?>
    <style type="text/tailwindcss">
        <?= file_get_contents(__DIR__ . '/assets/css/product-card.css') ?>
    </style>

    <div class="container mx-auto p-4 flex flex-col gap-4 max-w-4xl">
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full">
            <div class="flex flex-col gap-2 md:gap-4">
                <?= render_image_viewer($_GET['image'] ?? $images[0]['image']) ?>

                <div class="flex flex-row gap-2 overflow-x-auto sm:w-60 lg:w-96">
                    <?php foreach ($images as $image): ?>
                        <div hx-get="<?= base_url('/product.php', ['id' => $product['id'], 'action' => 'change-image', 'image' => $image['image']]) ?>"
                             hx-target="#image-viewer" hx-swap="outerHTML"
                             class="aspect-square bg-cover rounded-xl sm:h-16 lg:h-20 cursor-pointer"
                             style="background-image: url('<?= base_url("/images/" . $image['image']) ?>');">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex flex-col gap-4 flex-grow self-stretch">
                <div class="flex flex-col flex-grow">
                    <div class="flex flex-row gap-2 justify-between w-full items-start">
                        <h2 class="text-3xl w-full text-neutral-300"><?= htmlspecialchars($product['name']) ?></h2>
                        <span class="text-3xl text-neutral-300 font-bold"><?= htmlspecialchars($product['price']) ?>zł</span>
                    </div>
                    <p class="text-neutral-200 text-lg flex-grow"><?= htmlspecialchars($product['description']) ?></p>
                </div>

                <div class="flex flex-row gap-2 justify-end items-center">
                    <?php if (auth_is_logged_in()): ?>
                        <?= render_favourites_button($product, $isInFavourites) ?>
                    <?php endif; ?>

                    <?= render_add_to_cart_button($product) ?>
                </div>
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