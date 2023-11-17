<?php

require_once __DIR__ . "/../../tooling/autoload.php";

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

function get_parameter_value_for_product(string $productId, string $parameterId): ?string
{
    if (isset($editSessionData['new_parameters'][$parameterId])) return null;

    $data = db_query_row(
        get_db_connection(),
        "SELECT id, name, pi.value FROM parameters INNER JOIN products_have_parameters pi ON parameters.id = pi.parameter_id AND pi.product_id = ? AND pi.parameter_id = ?",
        [$productId, $parameterId]
    );

    if ($data === null) return null;

    return $data['value'];
}

function get_parameter_data(array $editSessionData, string $parameterId): array|null
{
    if (isset($editSessionData['new_parameters'][$parameterId])) return $editSessionData['new_parameters'][$parameterId];

    return db_query_row(get_db_connection(), "SELECT * FROM parameters WHERE id = ?", [$parameterId]);
}

function does_parameter_exist(array $editSessionData, string $parameterId): bool
{
    return get_parameter_data($editSessionData, $parameterId) !== null;
}

function does_parameter_exist_for_product(string $productId, string $parameterId): bool
{
    return get_parameter_value_for_product($productId, $parameterId) !== null;
}

function is_parameter_already_in_edit_session(array $editSessionData, string $parameterId): bool
{
    return count(array_values(array_filter($editSessionData['elements'], function ($e) use ($parameterId) {
            if ($e['type'] !== 'input_parameter') return false;
            if ($e['parameter_id'] !== $parameterId) return false;
            return true;
        }))) > 0;
}

function is_new_parameter_name_input_in_edit_session(array $editSessionData): bool
{
    return count(array_filter($editSessionData['elements'], function ($element) {
            if ($element['type'] !== 'new_parameter_input') return false;
            return true;
        })) > 0;
}

function is_choose_parameter_already_in_edit_session(array $editSessionData): bool
{
    return in_array("choose_parameter", array_map(fn($e) => $e['type'], $editSessionData['elements']));
}

function get_choose_parameter_select_element(array $editSessionData): ?array
{
    foreach ($editSessionData['elements'] as $element) {
        if ($element['type'] === 'choose_parameter') return $element;
    }

    return null;
}

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(config("app.url") . "/management/products.php");
if (!is_numeric($id)) redirect_and_kill(config("app.url") . "/management/products.php");
$id = intval($id);

$product = db_query_row(get_db_connection(), "SELECT * FROM products WHERE id = ?", [$id]);
if ($product === null) redirect_and_kill(config("app.url") . "/management/products.php");

if (!isset($_GET['edit_session'])) redirect_and_kill(config("app.url") . "/management/products/edit.php?id=$id&edit_session=" . uniqid());
$editSessionId = $_GET['edit_session'];

$editSessionData = session_get("edit_session_" . $id . "_$editSessionId", [
    'elements' => []
]);

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (!old_input_has("name")) old_input_add("name", $product['name']);
    if (!old_input_has("description")) old_input_add("description", $product['description']);
    if (!old_input_has("price")) old_input_add("price", $product['price']);
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
    old_input_add("name", $_POST['name']);
    old_input_add("description", $_POST['description']);
    old_input_add("price", $_POST['price']);
}

if (!session_has("edit_session_" . $id . "_$editSessionId")) {
    $parameters = db_query_rows(get_db_connection(), "SELECT parameters.id, parameters.name, pi.value FROM parameters LEFT JOIN products_have_parameters pi ON parameters.id = pi.parameter_id AND pi.product_id = ?", [$id]);

    foreach ($parameters as $parameter) {
        if (!old_input_has("parameter_" . $parameter['id'])) {
            old_input_add("parameter_" . $parameter['id'], $parameter['value']);
        }

        if (!session_has("edit_session_" . $id . "_$editSessionId") && $parameter['value'] !== null) {
            $editSessionData['elements'][] = [
                'type' => 'input_parameter',
                'id' => uniqid(),
                'name' => 'parameter_' . $parameter['id'],
                'parameter_id' => $parameter['id'],
                'parameter_name' => $parameter['name']
            ];
        }
    }
}

$action = $_GET['action'] ?? null;

$thisUrl = config("app.url") . "/management/products/edit.php?id=$id&edit_session=$editSessionId";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if ($action === "add_parameter") {
        if (is_choose_parameter_already_in_edit_session($editSessionData)) redirect_and_kill($thisUrl);

        $parameters = db_query_rows(get_db_connection(), "SELECT parameters.id, parameters.name, pi.value FROM parameters LEFT JOIN products_have_parameters pi ON parameters.id = pi.parameter_id AND pi.product_id = ?", [$id]);

        $parameters = array_map(fn($parameter) => [
            "text" => $parameter['name'],
            "value" => $parameter['id']
        ], array_filter($parameters, fn($parameter) => is_parameter_already_in_edit_session($editSessionData, $parameter['id']) === false));

        $parameters[] = [
            'text' => 'Dodaj nowy parametr',
            'value' => '*new_parameter*'
        ];

        $editSessionData['elements'][] = [
            'type' => 'choose_parameter',
            'id' => uniqid(),
            'name' => 'choose_parameter',
            'options' => $parameters
        ];

        session_set_ttl("edit_session_" . $id . "_$editSessionId", $editSessionData, 60 * 30);
    } else if ($action === "resign_choose_parameter") {
        if (!in_array("choose_parameter", array_map(fn($e) => $e['type'], $editSessionData['elements']))) redirect_and_kill($thisUrl);

        $editSessionData['elements'] = array_filter($editSessionData['elements'], fn($e) => $e['type'] !== 'choose_parameter');

        session_set_ttl("edit_session_" . $id . "_$editSessionId", $editSessionData, 60 * 30);
    } else if ($action === "confirm_choose_parameter") {
        $chooseParameterElement = get_choose_parameter_select_element($editSessionData);
        if (!$chooseParameterElement === null) redirect_and_kill($thisUrl);

        // check if parameter id is set in post body
        $parameterId = $_POST["choose_parameter_" . $chooseParameterElement['id']] ?? null;
        if ($parameterId === null) redirect_and_kill($thisUrl);

        if ($parameterId !== "*new_parameter*") {
            if (!does_parameter_exist($editSessionData, $parameterId)) redirect_and_kill($thisUrl);

            if (is_parameter_already_in_edit_session($editSessionData, $parameterId)) redirect_and_kill($thisUrl);

            // remove choose parameter from edit session
            $editSessionData['elements'] = array_filter($editSessionData['elements'], fn($e) => $e['type'] !== 'choose_parameter');

            $parameter = get_parameter_data($editSessionData, $parameterId);

            if (!old_input_has("parameter_" . $parameterId)) {
                $value = get_parameter_value_for_product($id, $parameterId);
                old_input_add("parameter_" . $parameterId, $value);
            }

            $editSessionData['elements'][] = [
                'type' => 'input_parameter',
                'id' => uniqid(),
                'name' => 'parameter_' . $parameterId,
                'parameter_id' => $parameterId,
                'parameter_name' => $parameter['name']
            ];
        } else {
            if (is_new_parameter_name_input_in_edit_session($editSessionData)) redirect_and_kill($thisUrl);

            $editSessionData['elements'] = array_filter($editSessionData['elements'], fn($e) => $e['type'] !== 'choose_parameter');

            $editSessionData['elements'][] = ['type' => 'new_parameter_input'];
        }

        session_set_ttl("edit_session_" . $id . "_$editSessionId", $editSessionData, 60 * 30);
    } else if ($action === "remove_parameter") {
        $parameterId = $_GET['parameter_id'] ?? null;
        if ($parameterId === null) redirect_and_kill($thisUrl);

        if ($parameterId !== "*new_parameter*") {
            if (!is_parameter_already_in_edit_session($editSessionData, $parameterId)) redirect_and_kill($thisUrl);

            $editSessionData['elements'] = array_filter($editSessionData['elements'], function ($element) use ($parameterId) {
                if ($element['type'] === 'input_parameter' && $element['parameter_id'] === $parameterId) return false;
                return true;
            });
        } else {
            if (!is_new_parameter_name_input_in_edit_session($editSessionData)) redirect_and_kill($thisUrl);

            $editSessionData['elements'] = array_filter($editSessionData['elements'], function ($element) use ($parameterId) {
                if ($element['type'] === 'new_parameter_input') return false;
                return true;
            });

            if (isset($editSessionData['new_parameters'][$parameterId])) {
                unset($editSessionData['new_parameters'][$parameterId]);
            }
        }

        session_set_ttl("edit_session_" . $id . "_$editSessionId", $editSessionData, 60 * 30);
    } else if ($action === "confirm_new_parameter") {
        if (!is_new_parameter_name_input_in_edit_session($editSessionData)) redirect_and_kill($thisUrl);

        $newParameterName = $_POST['new_parameter_name'] ?? null;
        if ($newParameterName === null) redirect_and_kill($thisUrl);
        $newParameterName = trim($newParameterName);
        if ($newParameterName === "") {
            old_input_add("new_parameter_name", $newParameterName);
            validation_errors_add("new_parameter_name", "Pole nazwa jest wymagane.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        if (strlen($newParameterName) > 64) {
            old_input_add("new_parameter_name", $newParameterName);
            validation_errors_add("new_parameter_name", "Pole nazwa może mieć maksimum 64 znaki.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $newParameterId = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $newParameterName)));
        $newParameterId = preg_replace('/[\\-]+/u', "-", $newParameterId);
        while (str_starts_with($newParameterId, "-")) $newParameterId = substr($newParameterId, 1);
        while (str_ends_with($newParameterId, "-")) $newParameterId = substr($newParameterId, 0, -1);

        $number = 1;
        if (does_parameter_exist($editSessionData, $newParameterId)) {
            while (does_parameter_exist($editSessionData, $newParameterId . "-$number")) {
                $number += 1;
            }

            $newParameterId = $newParameterId . "-$number";
        }

        $editSessionData['new_parameters'][] = [
            'id' => $newParameterId,
            'name' => $newParameterName
        ];

        $editSessionData['elements'] = array_filter($editSessionData['elements'], function ($element) {
            if ($element['type'] === 'new_parameter_input') return false;
            return true;
        });

        $editSessionData['elements'][$newParameterId] = [
            'type' => 'input_parameter',
            'id' => uniqid(),
            'name' => 'parameter_' . $newParameterId,
            'parameter_id' => $newParameterId,
            'parameter_name' => $newParameterName
        ];

        session_set_ttl("edit_session_" . $id . "_$editSessionId", $editSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "submit") {
        $name = $_POST['name'] ?? null;
        $description = $_POST['description'] ?? null;
        $price = $_POST['price'] ?? null;

        if ($name === null) validation_errors_add("name", "Pole nazwa jest wymagane.");
        if ($description === null) validation_errors_add("description", "Pole opis jest wymagane.");
        if ($price === null) validation_errors_add("price", "Pole cena jest wymagane.");

        if (!validation_errors_is_empty()) {
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $parameterNames = array_filter(array_keys($_POST), fn($parameter) => str_starts_with($parameter, "parameter_"));
        $parameterNames = array_map(fn($parameter) => substr($parameter, strlen("parameter_")), $parameterNames);
        $parameterNames = array_filter($parameterNames, fn($name) => does_parameter_exist($editSessionData, $name));

        $parameters = [];
        foreach ($parameterNames as $parameterName) {
            $parameters[$parameterName] = trim($_POST["parameter_" . $parameterName]);
            if ($parameters[$parameterName] === "") {
                validation_errors_add("parameter_" . $parameterName, "Pole jest wymagane.");
                redirect_and_kill($thisUrl . "&render_without_layout=true");
            }
        }

        db_transaction(function (mysqli $db) use ($name, $description, $price, $id, $parameters, $editSessionData) {
            db_execute_stmt($db, "UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?", [
                $name, $description, $price, $id
            ]);

            db_execute_stmt($db, "DELETE FROM products_have_parameters WHERE product_id = ?", [$id]);

            if (count($parameters) > 0) {
                $queryParts = array_map(fn($parameterId) => "(?, ?, ?)", $parameters);
                $queryParts = join(', ', $queryParts);

                $queryValues = [];
                foreach ($parameters as $parameterId => $parameterValue) {
                    $queryValues[] = $id;
                    $queryValues[] = $parameterId;
                    $queryValues[] = $parameterValue;

                    if (isset($editSessionData['new_parameters'][$parameterId])) {
                        db_execute_stmt($db, "INSERT INTO parameters (id, name) VALUES (?, ?)", [$parameterId, $editSessionData['new_parameters'][$parameterId]['name']]);
                    }
                }

                db_execute_stmt($db, "INSERT INTO products_have_parameters (product_id, parameter_id, value) VALUES " . $queryParts, $queryValues);
            }
        });

        session_flash("after_product_edit", true);
        session_remove("edit_session_" . $id . "_$editSessionId");
        redirect_and_kill($thisUrl);
    }
}

ob_start(); ?>
    <form method="POST" action="<?= $thisUrl ?>&action=submit"
        id="edit-product-form"
        class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Edytowanie produktu</h1>

        <img src="https://placehold.co/400x400" alt="Product image" class="w-full aspect-square rounded-xl"/>

        <div class="bg-neutral-800 border-4 border-neutral-800 relative rounded-xl">
            <label for="image" class="p-4 w-full flex flex-row gap-4 rounded-xl text-neutral-300 w-full h-full">Wybrano
                zdjęcie: </label>
            <input type="file" name="image" id="image" class="w-full h-full absolute top-0 left-0 invisible"/>
        </div>

        <div class="flex flex-col gap-4" id="edit-product-form-body">
            <?= render_textfield(label: 'Nazwa', name: 'name', type: 'text') ?>
            <?= render_textfield(label: 'Opis', name: 'description', type: 'textarea') ?>
            <?= render_textfield(label: 'Cena', name: 'price', type: 'number') ?>

            <?php foreach ($editSessionData['elements'] as $element): ?>
                <?php if ($element['type'] === 'choose_parameter'): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_select("Wybierz parametr", "choose_parameter_" . $element['id'], options: $element['options'], id: $element['id']) ?>

                        <div hx-post="<?= $thisUrl ?>&action=confirm_choose_parameter"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                        </div>

                        <div hx-post="<?= $thisUrl ?>&action=resign_choose_parameter"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                        </div>
                    </div>
                <?php elseif ($element['type'] === "input_parameter"): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_textfield($element['parameter_name'], $element['name'], id: $element['id']) ?>

                        <div hx-post="<?= $thisUrl ?>&action=remove_parameter&parameter_id=<?= $element['parameter_id'] ?>"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                        </div>
                    </div>
                <?php elseif ($element['type'] === "new_parameter_input"): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_textfield("Podaj nazwę nowego parametru", 'new_parameter_name', oldInput: false) ?>

                        <div hx-post="<?= $thisUrl ?>&action=confirm_new_parameter"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                        </div>

                        <div hx-post="<?= $thisUrl ?>&action=remove_parameter&parameter_id=<?= urlencode("*new_parameter*") ?>"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!in_array("choose_parameter", array_map(fn($e) => $e['type'], $editSessionData['elements'])) &&
                !is_new_parameter_name_input_in_edit_session($editSessionData)): ?>
                <div hx-post="<?= $thisUrl ?>&action=add_parameter"
                     hx-include="form"
                     hx-target="form"
                     hx-swap="outerHTML"
                     class="flex flex-row gap-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer p-4 rounded-xl">
                    <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                    Dodaj parametr
                </div>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
            <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zapisz</button>
        </div>
    </form>
<?php
$formContent = ob_get_clean();

if ($_SERVER['REQUEST_METHOD'] === "GET" && ($_GET['render_without_layout'] ?? "false") !== "true") {
    echo render_in_layout(function () use ($formContent) { ?>
        <div class="flex justify-center items-center p-4">
            <?= $formContent ?>
        </div>
    <?php });
} else {
    echo $formContent;
}