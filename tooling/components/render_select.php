<?php

function render_select(string           $label,
                       string           $name,
                       array            $options,
                       bool|null|string $validationError = null,
                       bool|null|string $oldInput = null,
                       ?string          $id = null
): string
{
    if ($id === null) $id = uniqid("select_");

    if ($oldInput !== false && gettype($oldInput) !== "string") {
        $oldInput = old_input_has($name) ? old_input_get($name) : "";
    }

    if ($validationError !== false && gettype($validationError) !== "string") {
        $validationError = validation_errors_get($name);

        if ($validationError !== null) {
            $validationError = sprintf("<span class=\"text-red-400 font-bold\">%s</span>", htmlspecialchars($validationError));
        }
    }

    $label = htmlspecialchars($label);
    $name = htmlspecialchars($name);
    $id = htmlspecialchars($id);

    ob_start();

    ?>
    <div class="flex flex-col gap-1 w-full ">
        <label for="<?= $id ?>" class="text-lg text-neutral-300 font-semibold mx-2"><?= $label ?></label>
        <select name="<?= $name ?>" id="<?= $id ?>"
                class="w-full p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"
        >
            <?php foreach ($options as $option): ?>
                <option value="<?= htmlspecialchars($option['value']) ?>"
                    <? if ($oldInput === $option['value']): ?>
                        selected
                    <?php endif; ?>
                ><?= htmlspecialchars($option['text']) ?></option>
            <?php endforeach; ?>

        </select>
        <?= $validationError ?>
    </div>
    <?php

    return ob_get_clean();
}