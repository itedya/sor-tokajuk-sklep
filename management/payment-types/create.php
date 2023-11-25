<?php

require_once __DIR__ . '/../../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

echo render_in_layout(function() { ?>
<div class="container mx-auto">
    <form class="flex flex-col gap-4 max-w-3xl mx-auto">
        <h2 class="text-3xl font-bold text-neutral-300 text-center">Stwórz metodę płatności</h2>

        <?= render_textfield(label: "Nazwa", name: "name") ?>
        
        <div class="flex flex-row w-full justify-end">
            <button class="px-8 py-2 bg-green-600 text-neutral-200 rounded-xl font-bold">Dodaj</button>
        </div>
    </form>
</div>
<?php });
