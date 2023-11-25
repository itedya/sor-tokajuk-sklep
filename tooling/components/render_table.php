<?php

function render_table(array $columns, array $rows): string
{
    $columnClasses = join(' ', ['bg-neutral-800', 'text-left', 'font-normal', 'p-2', 'whitespace-nowrap', 'text-neutral-200']);
    $valueClasses = join(' ', ['border-t', 'border-neutral-700', 'bg-neutral-900', 'p-2', 'text-neutral-400']);

    ob_start(); ?>
    <div class="overflow-x-auto w-full">
        <table class="table-auto bg-neutral-800 w-full border-separate border-spacing-0 rounded-xl border border-neutral-700">
            <?php if (count($columns) !== 0): ?>
                <thead>
                <tr>
                    <?php 
                        foreach ($columns as $index => $column):
                            $tempColumnClasses = $columnClasses;
                            if ($index === 0) $tempColumnClasses .= " rounded-tl-xl";
                            if ($index === count($columns) - 1) $tempColumnClasses .= " rounded-tr-xl";
                    ?>
                        <th class="<?= $tempColumnClasses ?>">
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
                        <td class="<?= $valueClasses ?>"><?= ($field['is_html'] ?? false) ? $field['value'] : htmlspecialchars($field['value']) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php return ob_get_clean();
}
