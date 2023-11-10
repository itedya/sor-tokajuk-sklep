<?php

function render_textfield(string           $label,
                          string           $name,
                          string           $type = 'text',
                          bool|null|string $validationError = null,
                          bool|null|string $oldInput = null,
                          ?string          $id = null
): string
{
    if ($id === null) $id = uniqid("input_");

    if ($oldInput !== false && gettype($validationError) !== "string") {
        $oldInput = old_input_has($name) ? old_input_get($name) : "";
    }

    if ($validationError !== false && gettype($validationError) !== "string") {
        $validationError = validation_errors_get($name);

        if ($validationError !== null) {
            $validationError = sprintf("<span class=\"text-red-400 font-bold\">%s</span>", htmlspecialchars($validationError));
        }
    }

    $label = htmlspecialchars($label);
    $type = htmlspecialchars($type);
    $name = htmlspecialchars($name);
    $id = htmlspecialchars($id);
    $oldInput = htmlspecialchars($oldInput);

    ob_start();
    ?>
    <div class="flex flex-col gap-1">
        <label for="<?= $id ?>" class="text-lg text-neutral-300 font-semibold mx-2"><?= $label ?></label>
        <input type="<?= $type ?>" name="<?= $name ?>" id="<?= $id ?>"
               class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
               value="<?= $oldInput ?>"
        />
        <?= $validationError ?>
    </div>
    <?php
    return ob_get_clean();
}