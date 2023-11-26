<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$backUrl = $_GET['back_url'] ?? base_url('/management/delivery-methods.php');
if (!is_array(parse_url($backUrl))) redirect_and_kill(base_url('/management/delivery-methods.php'));

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_POST['id'] ?? null;
    if (!is_numeric($id)) validation_errors_add("id", "ID jest wymagane");

    if (!validation_errors_is_empty()) redirect_and_kill(base_url('/management/delivery-methods.php'));

    $id = intval($id);

    db_transaction(function (mysqli $db) use ($id) {
        $stmt = db_execute_stmt($db, "UPDATE delivery_methods SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL", [$id]);
        if ($stmt->affected_rows !== 1) {
            redirect_and_kill(base_url('/management/delivery-methods.php'));
        }
    });

    redirect_and_kill($backUrl);
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill($backUrl);
if (!is_numeric($id)) redirect_and_kill($backUrl);

$id = intval($id);

$db = get_db_connection();
if (database_delivery_methods_get_by_id($db, $id) === null) redirect_and_kill($backUrl);
$db->close();

echo render_in_layout(function () use ($backUrl, $id) { ?>
    <div class="container mx-auto">
        <div class="flex flex-col gap-4 justify-center items-center">
            <h2 class="text-3xl text-center text-neutral-300">Potwierdzenie</h2>
            
            <p class="text-neutral-200">Czy na pewno chcesz usunąć tą metodę dostawy?</p>

            <div class="flex flex-row gap-4 justify-center items-center">
                <a class="rounded-xl px-8 py-2 bg-neutral-600 text-neutral-200 font-bold" href="<?= htmlspecialchars($backUrl) ?>">Nie, wróć tam gdzie byłem</a>
                <form method="POST" action="<?= htmlspecialchars(base_url('/management/delivery-methods/delete.php', ['back_url' => $backUrl])) ?>">
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <button type="submit" class="rounded-xl px-8 py-2 bg-red-600 text-neutral-200 font-bold">Tak</button>
                </form>
            </div>
        </div>
    </div>
<?php });
