<?php

require_once './tooling/autoload.php';

$db = get_db_connection();
$products = database_products_get($db);
$db->close();

echo render_in_layout(function () use ($products) { ?>
    <style type="text/tailwindcss">
        <?= file_get_contents(__DIR__ . '/../../assets/css/product-card.css') ?>
    </style>

    <div class="container mx-auto">
        <div class="grid grid-cols-1 auto-grid-rows gap-8 xl:grid-cols-2">
            <?php foreach ($products as $product): ?>
                <div class="item"
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
