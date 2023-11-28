<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_get('after_additional_page_update', false)) {
    echo render_in_layout(function () { ?>
        <div class="container mx-auto">
            <div class="flex flex-col gap-4 justify-center items-center">
                <h2 class="text-3xl font-bold text-neutral-300">Sukces</h2>
                <p class="text-neutral-200">Pomyślnie edytowano stronę</p>
                <a href="<?= base_url('/management/additional-pages.php') ?>"
                   class="px-8 py-2 text-neutral-200 bg-blue-600 rounded-xl font-bold">Przejdź do listy stron</a>
            </div>
        </div>
    <?php });
    return;
}

$id = null;
if ($_SERVER['REQUEST_METHOD'] === "POST") $id = $_POST['id'] ?? null;
else $id = $_GET['id'] ?? null;

if ($id === null) redirect_and_kill(base_url('/management/additional-pages.php'));

$db = get_db_connection();
$page = database_additional_pages_get_by_id($db, $id);
if ($page === null) redirect_and_kill(base_url('/management/additional-pages.php'));

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $validationCallbackUrl = base_url('/management/additional-pages/edit.php', ['render_without_layout' => 1, 'id' => $id]);

    foreach ($_POST as $key => $value) old_input_add($key, $value);

    if (empty($_POST['blocks'])) validation_errors_add("text", "Pole z tekstem nie może być puste.");
    if (empty($_POST['name'])) validation_errors_add("name", "Nazwa jest wymagana");

    if (!validation_errors_is_empty()) redirect_and_kill($validationCallbackUrl);

    $blocks = $_POST['blocks'];
    $name = $_POST['name'];

    if (gettype($blocks) !== "string") validation_errors_add("text", "Pole z tekstem musi zawierać tekst");
    if (!is_string($name)) validation_errors_add("name", "Nazwa musi być tekstem.");

    if (!validation_errors_is_empty()) redirect_and_kill($validationCallbackUrl);

    $blocks = json_decode($blocks, true);
    if ($blocks === null) validation_errors_add("blocks", "Pole z tekstem musi zawierać tekst");
    if (strlen($name) < 3) validation_errors_add("name", "Nazwa musi mieć więcej niż 3 znaki");
    if (strlen($name) > 64) validation_errors_add("name", "Nazwa musi mieć mniej niż 64 znaki");

    if (!validation_errors_is_empty()) redirect_and_kill($validationCallbackUrl);

    db_transaction(function (mysqli $db) use (&$id, $name) {
        database_additional_pages_update($db, $id, $name);
    });

    file_put_contents(__DIR__ . '/../../additional-pages/' . $id, json_encode($blocks));

    session_flash('after_additional_page_update', true);
    http_response_code(201);
    return;
}

if (!old_input_has("name")) old_input_add("name", $page['name']);
if (!old_input_has("blocks")) {
    old_input_add("blocks", file_get_contents(__DIR__ . '/../../additional-pages/' . $id));
}

ob_start(); ?>
    <style>
        .ce-inline-tool {
            color: black;
        }

        .codex-editor {
            padding: 12px;
            border: 1px solid lightgrey;
            border-radius: 12px;
        }

        .codex-editor__redactor {
            padding: 12px 0 !important;
        }
    </style>

    <form class="flex flex-col gap-8 max-w-3xl mx-auto"
          action="<?= base_url('/management/additional-pages/edit.php') ?>" method="POST">
        <h2 class="text-center text-3xl font-bold text-neutral-300">Edytuj nową stronę</h2>

        <?= render_textfield(label: "Nazwa", name: "name") ?>

        <div class="flex flex-col gap-1 w-full">
            <span class="text-lg text-neutral-300 font-semibold mx-2">Tekst</span>
            <div class="text-neutral-200 w-full stroke-neutral-800 border-neutral-800 max-w-3xl mx-auto"
                 id="editorjs"></div>
            <?php if (validation_errors_has("text")): ?>
                <span class="text-red-400 font-bold text-lg"><?= htmlspecialchars(validation_errors_get("text")) ?></span>
            <?php endif ?>
        </div>

        <div class="flex flex-row justify-end w-full items-center mx-auto max-w-3xl">
            <button class="px-8 py-2 bg-yellow-600 text-neutral-200 rounded-xl font-bold">Zapisz</button>
        </div>
    </form>
<?php
$content = ob_get_clean();

ob_start(); ?>
    let editor = new EditorJS({
    holder: 'editorjs',
<?php if (old_input_has("blocks")): ?>
    data: JSON.parse(`<?= old_input_get("blocks") ?>`)
<?php endif; ?>
    });

    const form = document.querySelector("form");
    form.addEventListener("submit", (e) => {
    e.preventDefault();
    const nameInput = document.querySelector('[name="name"]');

    editor.save()
    .then(outputData => {
    const formData = new FormData();
    formData.append("id", `<?= $id ?>`);
    formData.append("name", nameInput.value);
    formData.append("blocks", JSON.stringify(outputData));
    return formData
    })
    .then(formData => fetch('edit.php', {
    method: "POST",
    body: formData,
    }))
    .then(async res => {
    if (res.status === 201) {
    window.location.reload();
    return;
    }

    editor.destroy();

    let {data, js} = await res.json();
    e.target.outerHTML = data;
    eval(js);
    });
    });
<?php
$js = ob_get_clean();

if (!isset($_GET['render_without_layout'])) {
    echo render_in_layout(function () use ($content, $js) { ?>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
        <div class="container mx-auto">
            <?= $content ?>
            <script><?= $js ?></script>
        </div>
    <?php });
} else {
    header("Content-Type: application/json");
    echo json_encode([
        'data' => $content,
        'js' => $js
    ]);
}
