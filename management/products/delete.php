<?php

require_once __DIR__ . '/../../tooling/autoload.php';

if (!isset($_GET['id'])) redirect_and_kill("/management/products.php");
$id = $_GET['id'];

if (!is_numeric($id)) redirect_and_kill("/management/products.php");

$id = intval($id);
$productName = "Trumna drewniana";

echo render_in_layout(function () use ($id, $productName) { ?>
    <div class="text-3xl text-center text-neutral-300 p-4">
        Czy na pewno chcesz usunąć produkt <?= htmlspecialchars($productName) ?>?
    </div>

    <div class="flex flex-row justify-center items-center gap-4">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>"/>

            <button class="px-8 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg">
                Usuń
            </button>
        </form>

        <a href="<?= config("app.url") . "/management/products.php" ?>"
           class="px-8 py-2 bg-neutral-200 text-neutral-900 font-semibold rounded-lg">
            Anuluj
        </a>
    </div>
<?php });
