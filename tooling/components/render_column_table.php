<?php

function render_column_table(array $columns): string
{
    $columnClasses = join(' ', ['border', 'border-neutral-700', 'bg-neutral-800', 'text-left', 'font-normal', 'p-2', 'whitespace-nowrap']);
    $valueClasses = join(' ', ['border', 'border-neutral-700', 'bg-neutral-900', 'p-2', 'text-neutral-400']);

    ob_start(); ?>
    <div class="overflow-x-auto w-full">
        <table class="border-collapse border border-neutral-700 bg-neutral-800 w-full">
            <tbody>
            <?php foreach ($columns as $column): ?>
                <tr>
                    <?php foreach ($column as $row): ?>
                        <?php if ($row['type'] === "COLUMN"): ?>
                            <th class="<?= $columnClasses ?>">
                                <?= ($row['is_html'] ?? false) ? $row['value'] : htmlspecialchars($row['value']) ?>
                            </th>
                        <?php else: ?>
                            <td class="<?= $valueClasses ?>">
                                <?= ($row['is_html'] ?? false) ? $row['value'] : htmlspecialchars($row['value']) ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php return ob_get_clean();
}