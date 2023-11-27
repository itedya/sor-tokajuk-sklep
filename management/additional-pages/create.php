<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

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

<form class="flex flex-col gap-8" action="<?=base_url('/management/additional-pages/create.php') ?>" method="POST">
    <h2 class="text-center text-3xl font-bold text-neutral-300">Dodaj nową stronę</h2>

    <div class="text-neutral-200 w-full stroke-neutral-800 border-neutral-800 max-w-3xl mx-auto" id="editorjs"></div>

    <div class="flex flex-row justify-end w-full items-center mx-auto max-w-3xl">
        <button class="px-8 py-2 bg-green-600 text-neutral-200 rounded-xl font-bold">Dodaj</button>
    </div>

    <script>
        (() => {
            let editor = new EditorJS({ holder: 'editorjs' });

            const form = document.querySelector("form");
            form.addEventListener("submit", (e) => {
                e.preventDefault();

                editor.save()
                    .then(outputData => fetch('create.php', {
                        method: "POST",
                        body: JSON.stringify({ block: outputData.blocks })
                    }))
                    .then(async res => {
                        e.target.outerHTML = await res.text();
                        new EditorJS({ holder: 'editorjs' });
                    });
            });
        })();
    </script>
</form>

<?php
$content = ob_get_clean();

if (!isset($_GET['render_without_layout'])) {
    echo render_in_layout(function() use ($content) { ?>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <div class="container mx-auto">
        <?= $content ?>
    </div>
    <?php });
} else {
    echo $content;
}
