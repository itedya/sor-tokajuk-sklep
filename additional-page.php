<?php

require_once __DIR__ . '/tooling/autoload.php';

$id = $_GET['id'] ?? null;
if ($id === null) abort(404);

$db = get_db_connection();
$page = database_additional_pages_get_by_id($db, $id);
$db->close();

if ($page === null) abort(404);

$content = file_get_contents(__DIR__ . '/additional-pages/' . $page['id']);

echo render_in_layout(function () use ($page, $content) { ?>
    <style>
        .ce-inline-tool {
            color: black;
        }

        .codex-editor {
            padding: 0;
        }

        .codex-editor__redactor {
            padding: 0 0 !important;
        }

        .ce-block__content {
            max-width: 100% !important;
        }
    </style>
    <div class="container mx-auto">
        <div class="flex flex-col max-w-3xl mx-auto gap-4">
            <h2 class="text-3xl font-bold text-neutral-300"><?= $page['name'] ?></h2>

            <div id="editorjs" class="text-neutral-200"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script>
        const editor = new EditorJS({
            holder: 'editorjs',
            readOnly: true,
            data: JSON.parse(`<?= $content ?>`)
        });
    </script>
<?php });