<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$backUrl = $_GET['back_url'] ?? base_url('/management/delivery-methods.php');
if (!is_array(parse_url($backUrl))) redirect_and_kill(base_url('/management/delivery-methods.php'));

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill($backUrl);
if (!is_numeric($id)) redirect_and_kill($backUrl);

$id = intval($id);

$db = get_db_connection();
if (database_delivery_methods_get_by_id($db, $id) === null) redirect_and_kill($backUrl);
$db->close();

ob_start();
?>
<form class="flex mx-auto flex-col gap-4 justify-center items-center max-w-3xl">
    <h2 class="text-3xl text-neutral-200 font-bold">Edytuj sposób dostawy</h2>

    <?= render_textfield(label: "Nazwa", name: "name") ?>
    <?= render_textfield(label: "Cena", name: "price", type: "number") ?>
    
    <div class="flex flex-row justify-end items-center w-full gap-4">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="px-8 py-2 bg-neutral-600 text-neutral-200 font-bold rounded-xl">Wróć do poprzedniej strony</a>
        <button hx-post="<?= base_url('/management/delivery-methods/edit.php') ?>"
                hx-target="form"
                hx-include="form"
                hx-trigger="click"
                class="px-8 py-2 bg-yellow-600 text-neutral-200 font-bold rounded-xl">Zapisz</button>
    </div>
</form>
<?php
$content = ob_get_clean();

if (boolval($_GET['render_in_layout'] ?? "false")) {
    echo render_in_layout(function() use ($content) { ?>
        <div class="container mx-auto p-4">
            <?= $content ?>
        </div>
<?php });
} else {
    echo $content;
}
