<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_POST['id'] ?? null;
    if ($id === null) redirect_and_kill(base_url('/management/additional-pages.php'));

    db_transaction(function ($db) use ($id) {
        if (database_additional_pages_exists_by_id($db, $id) === null) abort(404);
        database_additional_pages_delete_by_id($db, $id);
    });

    redirect_and_kill(base_url('/management/additional-pages.php'));
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(base_url('/management/additional-pages.php'));

$db = get_db_connection();
$page = database_additional_pages_get_by_id($db, $id);
$db->close();
if ($page === null) abort(404);

$name = $page['name'];

echo render_in_layout(function () use ($id, $name) { ?>
    <div class="container mx-auto">
        <div class="flex flex-col gap-4">
            <h2 class="text-3xl text-center text-neutral-300">Potwierdzenie</h2>

            <p class="text-neutral-200 text-center">Czy na pewno chcesz usunąć stronę <?= $name ?></p>

            <div class="flex flex-row gap-4 justify-center items-center">
                <a href="<?= base_url('/management/additional-pages.php') ?>"
                   class="bg-neutral-600 text-neutral-200 px-4 py-2 font-bold rounded-xl">Nie, wróć tam gdzie byłem</a>

                <form action="<?= base_url('/management/additional-pages/delete.php') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>"/>
                    <button class="bg-red-600 text-neutral-200 rounded-xl px-4 py-2 font-bold">Tak, usuń</button>
                </form>
            </div>
        </div>
    </div>
<?php });
