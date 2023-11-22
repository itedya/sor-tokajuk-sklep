<?php

function render_table(array $columns, array $rows): string
{
    $columnClasses = join(' ', ['border', 'border-neutral-700', 'bg-neutral-800', 'text-left', 'font-normal', 'p-2', 'whitespace-nowrap']);
    $valueClasses = join(' ', ['border', 'border-neutral-700', 'bg-neutral-900', 'p-2', 'text-neutral-400']);

    ob_start(); ?>
    <div class="overflow-x-auto w-full">
        <table class="border-collapse border border-neutral-700 bg-neutral-800 w-full">
            <?php if (count($columns) !== 0): ?>
                <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th class="<?= $columnClasses ?>">
                            <?= $column ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
                </thead>
            <?php endif; ?>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($row as $field): ?>
                        <td class="<?= $valueClasses ?>"><?= $field['value'] ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php return ob_get_clean();
}