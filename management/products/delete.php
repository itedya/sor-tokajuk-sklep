<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_has("product_deleted")) {
    echo render_in_layout(function () { ?>
        <div class="container p-4 flex flex-col gap-8 mx-auto">
            <h2 class="text-3xl text-center text-neutral-300">Produkt został usunięty.</h2>
            <div class="flex justify-center items-center">
                <a href="<?= config("app.url") . "/management/products.php" ?>"
                   class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">
                    Powrót do produktów
                </a>
            </div>
        </div>
    <?php });
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // get id
    if (!isset($_POST['id'])) redirect_and_kill(config("app.url") . "/management/products.php");
    $id = $_POST['id'];
    if (!is_numeric($id)) redirect_and_kill(config("app.url") . "/management/products.php");
    $id = intval($id);

    db_transaction(function (mysqli $db) use ($id) {
        $product = db_query_row($db, "SELECT * FROM products WHERE id = ?", [$id]);

        if ($product === null) redirect_and_kill(config("app.url") . "/management/products.php");

        db_execute_stmt($db, "DELETE FROM products_images WHERE product_id = ?", [$id]);
        db_execute_stmt($db, "DELETE FROM products WHERE id = ?", [$id]);
    });

    session_flash("product_deleted", true);
    redirect_and_kill(config("app.url") . "/management/products/delete.php");
} else {
    // get id
    if (!isset($_GET['id'])) redirect_and_kill(config("app.url") . "/management/products.php");
    $id = $_GET['id'];
    if (!is_numeric($id)) redirect_and_kill(config("app.url") . "/management/products.php");
    $id = intval($id);

    db_transaction(function (mysqli $db) use ($id, &$productName) {
        $result = db_query_row($db, "SELECT name FROM products WHERE id = ?", [$id]);
        if ($result === null) redirect_and_kill(config("app.url") . "/management/products.php");
        $productName = $result['name'];
    });

    echo render_in_layout(function () use ($id, $productName) { ?>
        <div class="text-3xl text-center text-neutral-300 p-4">
            Czy na pewno chcesz usunąć produkt <?= htmlspecialchars($productName) ?>?
        </div>

        <div class="flex flex-row justify-center items-center gap-4">
            <form action="<?= config("app.url") . "/management/products/delete.php" ?>" method="POST">
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
}