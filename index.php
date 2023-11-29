<?php

require_once './tooling/autoload.php';

$db = get_db_connection();

$action = $_GET['action'] ?? null;

if ($action === "render-products") {
    $category_id = $_GET['category_id'] ?? null;
    if ($category_id !== null) {
        $products = database_products_get_by_category_id($db, $category_id);
    } else {
        $products = database_products_get($db);
    }

    ob_start();
    foreach ($products as $product): ?>
        <div class="item item-hoverable"
             onclick="window.location.href = `<?= base_url('product.php', ['id' => $product['id']]) ?>`">
            <div class="item-img"
                 style="background-image: url('<?= base_url("/images/" . $product['image']) ?>');">
            </div>

            <h2 class="item-title"><?= htmlspecialchars($product['name']) ?></h2>
            <p class="item-description"><?= htmlspecialchars($product['description']) ?></p>
            <span class="item-price"><?= htmlspecialchars($product['price']) ?>zł</span>
        </div>
    <?php endforeach;
    $db->close();
    echo ob_get_clean();
    return;
}

$products = database_products_get($db);
$categories = db_query_rows($db, 'SELECT * FROM categories', []);

echo render_in_layout(function () use ($products, $categories, $db) { ?>
    <style type="text/tailwindcss">
        <?= file_get_contents(__DIR__ . '/assets/css/product-card.css') ?>
    </style>
    <div class="w-full h-52 relative flex flex-col justify-center gap-1">
        <div class="absolute h-full w-full bg-no-repeat bg-center bg-cover brightness-50 blur-lg"
             style="background-image: url('<?= base_url("/assets/img/cmentarz.png") ?>')"></div>

        <h2 class="text-neutral-300 text-4xl text-center z-10 font-bold">Witaj w TrumniXie!</h2>
        <p class="text-neutral-200 text-xl text-center z-10">Jak umierać, to tylko z nami.</p>
    </div>

    <div class="container mx-auto p-4 py-10 flex flex-col gap-8">
        <div class="w-full flex flex-col gap-4 md:flex-row" id="filtering-panel">
            <div class="flex flex-col md:flex-row flex-wrap gap-4 w-full">
                <?php foreach ($categories as $category): ?>
                    <button hx-get="<?= base_url('/index.php', [
                        'category_id' => $category['id'],
                        'action' => 'render-products'
                    ]) ?>" hx-target="#products-swappable-container" hx-swap="innerHTML"
                            class="rounded-xl text-center bg-neutral-800 text-neutral-200 px-8 py-2 hover:bg-neutral-700 duration-200">
                        <?= htmlspecialchars($category['name']) ?>
                    </button>
                <?php endforeach; ?>

                <button hx-get="<?= base_url('/index.php', [
                    'action' => 'render-products'
                ]) ?>" hx-target="#products-swappable-container" hx-swap="innerHTML"
                        class="rounded-xl text-center bg-neutral-800 text-neutral-200 px-8 py-2 hover:bg-neutral-700 duration-200">
                    Wszystkie produkty
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 auto-grid-rows gap-8 xl:grid-cols-2" id="products-swappable-container">
            <?php foreach ($products as $product): ?>
                <div class="item item-hoverable"
                     onclick="window.location.href = `<?= base_url('product.php', ['id' => $product['id']]) ?>`">
                    <div class="item-img"
                         style="background-image: url('<?= base_url("/images/" . $product['image']) ?>');">
                    </div>

                    <h2 class="item-title"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="item-description"><?= htmlspecialchars($product['description']) ?></p>
                    <span class="item-price"><?= htmlspecialchars($product['price']) ?>zł</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php });

$db->close();