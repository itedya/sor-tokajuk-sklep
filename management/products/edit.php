<?php

// Todo: kubuś zrób backend bo mnie ten javascript wykończy zaraz

require_once __DIR__ . "/../../tooling/autoload.php";

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(config("app.url") . "/management/products.php");
if (!is_numeric($id)) redirect_and_kill(config("app.url") . "/management/products.php");
$id = intval($id);

$product = db_query_row(get_db_connection(), "SELECT * FROM products WHERE id = ?", [$id]);
if ($product === null) redirect_and_kill(config("app.url") . "/management/products.php");

if (!old_input_has("name")) old_input_add("name", $product['name']);
if (!old_input_has("description")) old_input_add("description", $product['description']);
if (!old_input_has("price")) old_input_add("price", $product['price']);

echo render_in_layout(function () use ($id) { ?>
    <div class="flex justify-center items-center p-4">
        <form method="POST" action="<?= config("app.url") . "/management/products/edit.php" ?>"
              class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
            <input type="hidden" name="id" value="<?= $id ?>"/>

            <h1 class="text-4xl font-bold text-center text-neutral-300">Edytowanie produktu</h1>

            <img src="https://placehold.co/400x400" alt="Product image" class="w-full aspect-square rounded-xl"/>

            <div class="bg-neutral-800 border-4 border-neutral-800 relative rounded-xl">
                <label for="image" class="p-4 w-full flex flex-row gap-4 rounded-xl text-neutral-300 w-full h-full">Wybrano
                    zdjęcie: </label>
                <input type="file" name="image" id="image" class="w-full h-full absolute top-0 left-0 invisible"/>
            </div>

            <div class="flex flex-col gap-4">
                <?= render_textfield(label: 'Nazwa', name: 'name', type: 'text') ?>
                <?= render_textfield(label: 'Opis', name: 'description', type: 'textarea') ?>
                <?= render_textfield(label: 'Cena', name: 'price', type: 'number') ?>
            </div>

            <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zapisz</button>
            </div>
        </form>
    </div>
<?php });