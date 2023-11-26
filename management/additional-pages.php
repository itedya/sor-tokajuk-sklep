<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$pages = [
    [
        'id' => 'regulamin',
        'name' => 'Regulamin',
        'content' => 'Lorem ipsum dolor sit amet!'
    ],
    [
        'id' => 'sposoby-dostawy',
        'name' => 'Sposoby dostawy',
        'content' => 'Lorem ipsum dolor sit amet!',
    ]
];

$renderInLayout = !isset($_GET['render_without_layout']);

function render_actions_for_page(array $site): string
{
    ob_start(); ?>
    <div class="flex flex-row flex-wrap gap-2 p-2">
        <a href="<?= htmlspecialchars(base_url('/additional-pages.php', ['id' => $site['id']])) ?>"
            class="px-4 py-2 bg-neutral-600 text-neutral-200 rounded-xl font-bold">Odwiedź</a>
        <a href="<?= base_url('/management/additional-pages/edit.php', ['id' => $site['id']]) ?>" 
            class="px-4 py-2 bg-yellow-600 text-neutral-200 rounded-xl font-bold">
            Edytuj
        </a>
        <a href="<?= base_url('/management/additional-pages/delete.php', ['id' => $site['id']]) ?>"
            class="px-4 py-2 bg-red-600 text-neutral-200 rounded-xl font-bold">
            Usuń
        </a>
    </div>
<?php 
    return ob_get_clean();
}

if ($renderInLayout) {
    echo render_in_layout(function() use ($pages) { ?>
    <div class="container mx-auto">
        <div class="flex flex-col gap-4 justify-center items-center max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-neutral-300">Dodatkowe strony</h2>

            <?= render_table(['Nazwa', ''], array_map(function ($row) {
                return [
                    ['value' => $row['name']],
                    ['value' => render_actions_for_page($row), 'is_html' => true],
                ];
            }, $pages)) ?>

            <div class="flex flex-row justify-end items-center w-full">
                <a href="<?= base_url('/management/additional-pages/create.php') ?>" class="px-8 py-2 bg-green-600 text-neutral-200 rounded-xl font-bold">Dodaj nową stronę</a>
            </div>
        </div>
    </div>
<?php });
} else {
    echo $content;
}
