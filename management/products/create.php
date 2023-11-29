<?php

require_once __DIR__ . "/../../tooling/autoload.php";

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

if (session_has("after_product_creation")) {
    echo render_in_layout(function () { ?>
        <div class="flex flex-col justify-center items-center p-4 gap-8">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Sukces</h1>
            <p class="text-xl text-neutral-200 text-center">Pomyślnie stworzono produkt.</p>
            <a class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg disabled:bg-blue-400 duration-300"
               href="<?= base_url("/management/products.php") ?>">Powrót do listy produktów</a>
        </div>
    <?php });
    die();
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

function is_parameter_already_in_edit_session(array $editSessionData, string $parameterId): bool
{
    foreach ($editSessionData['elements'] as $element) {
        if ($element['type'] === 'input_parameter' && $element['parameter_id'] === $parameterId) return true;
    }

    return false;
}

function is_new_parameter_name_input_in_edit_session(array $editSessionData): bool
{
    foreach ($editSessionData['elements'] as $element) {
        if ($element['type'] === 'new_parameter_input') return true;
    }

    return false;
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

$db = get_db_connection();

if (!isset($_GET['create_product_session'])) {
    redirect_and_kill(base_url('/management/products/create.php', [
        'create_product_session' => uniqid()
    ]));
}

$createSessionId = $_GET['create_product_session'];
$sessionVariableName = "create_product_session_$createSessionId";

$createSessionData = session_get($sessionVariableName, [
    'elements' => [],
    'images' => [],
    'new_categories' => []
]);

if (!session_has($sessionVariableName)) {
    $categories = array_map(fn($category) => [
        'text' => $category['name'],
        'value' => strval($category['id'])
    ], db_query_rows($db, "SELECT * FROM categories", []));

    $categories[] = [
        'text' => 'Nowa kategoria',
        'value' => '*new_category*'
    ];

    $createSessionData['elements'][] = [
        'type' => 'choose_category',
        'options' => $categories
    ];

    session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
}

$action = $_GET['action'] ?? null;

$thisUrl = base_url('/management/products/create.php', ['create_product_session' => $createSessionId]);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    foreach (array_keys($_POST) as $key) old_input_add($key, $_POST[$key]);

    if (isset($_POST['category_id']) && is_numeric($_POST['category_id'])) {
        old_input_add("category_id", intval($_POST['category_id']));
    }

    if ($action === "add_parameter") {
        if (is_choose_parameter_already_in_edit_session($createSessionData)) redirect_and_kill($thisUrl);

        $parameters = db_query_rows($db, "SELECT id, name FROM parameters", []);

        $parameters = array_map(fn($parameter) => [
            "text" => $parameter['name'],
            "value" => $parameter['id']
        ], array_filter($parameters, fn($parameter) => is_parameter_already_in_edit_session($createSessionData, $parameter['id']) === false));

        $parameters[] = [
            'text' => 'Dodaj nowy parametr',
            'value' => '*new_parameter*'
        ];

        $createSessionData['elements'][] = [
            'type' => 'choose_parameter',
            'id' => uniqid(),
            'name' => 'choose_parameter',
            'options' => $parameters
        ];

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "resign_choose_parameter") {
        if (!is_choose_parameter_already_in_edit_session($createSessionData)) redirect_and_kill($thisUrl);

        $createSessionData['elements'] = array_filter($createSessionData['elements'], fn($e) => $e['type'] !== 'choose_parameter');

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "confirm_choose_parameter") {
        $chooseParameterElement = get_choose_parameter_select_element($createSessionData);
        if ($chooseParameterElement === null) redirect_and_kill($thisUrl);

        // check if parameter id is set in post body
        $parameterId = $_POST["choose_parameter_" . $chooseParameterElement['id']] ?? null;
        if ($parameterId === null) redirect_and_kill($thisUrl);

        if ($parameterId !== "*new_parameter*") {
            if (!does_parameter_exist($createSessionData, $parameterId)) redirect_and_kill($thisUrl);

            if (is_parameter_already_in_edit_session($createSessionData, $parameterId)) redirect_and_kill($thisUrl);

            // remove choose parameter from create session
            $createSessionData['elements'] = array_filter($createSessionData['elements'], fn($e) => $e['type'] !== 'choose_parameter');

            $parameter = get_parameter_data($createSessionData, $parameterId);

            if (!old_input_has("parameter_" . $parameterId)) {
                $value = $createSessionData['new_parameters'][$parameterId] ?? null;
                old_input_add("parameter_" . $parameterId, $value);
            }

            $createSessionData['elements'][] = [
                'type' => 'input_parameter',
                'id' => uniqid(),
                'name' => 'parameter_' . $parameterId,
                'parameter_id' => $parameterId,
                'parameter_name' => $parameter['name']
            ];
        } else {
            if (is_new_parameter_name_input_in_edit_session($createSessionData)) redirect_and_kill($thisUrl);

            $createSessionData['elements'] = array_filter($createSessionData['elements'], fn($e) => $e['type'] !== 'choose_parameter');

            $createSessionData['elements'][] = ['type' => 'new_parameter_input'];
        }

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "remove_parameter") {
        $parameterId = $_GET['parameter_id'] ?? null;
        if ($parameterId === null) redirect_and_kill($thisUrl);

        if ($parameterId !== "*new_parameter*") {
            if (!is_parameter_already_in_edit_session($createSessionData, $parameterId)) redirect_and_kill($thisUrl);

            $createSessionData['elements'] = array_filter($createSessionData['elements'], function ($element) use ($parameterId) {
                if ($element['type'] === 'input_parameter' && $element['parameter_id'] === $parameterId) return false;
                return true;
            });
        } else {
            if (!is_new_parameter_name_input_in_edit_session($createSessionData)) redirect_and_kill($thisUrl);

            $createSessionData['elements'] = array_filter($createSessionData['elements'], function ($element) use ($parameterId) {
                if ($element['type'] === 'new_parameter_input') return false;
                return true;
            });

            if (isset($createSessionData['new_parameters'][$parameterId])) {
                unset($createSessionData['new_parameters'][$parameterId]);
            }
        }

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "confirm_new_parameter") {
        if (!is_new_parameter_name_input_in_edit_session($createSessionData)) redirect_and_kill($thisUrl);

        $newParameterName = $_POST['new_parameter_name'] ?? null;
        if ($newParameterName === null) redirect_and_kill($thisUrl);

        $newParameterName = trim($newParameterName);

        if ($newParameterName === "") {
            validation_errors_add("new_parameter_name", "Pole nazwa jest wymagane.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        if (strlen($newParameterName) > 64) {
            validation_errors_add("new_parameter_name", "Pole nazwa może mieć maksimum 64 znaki.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $newParameterId = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $newParameterName)));
        $newParameterId = preg_replace('/[\\-]+/u', "-", $newParameterId);
        while (str_starts_with($newParameterId, "-")) $newParameterId = substr($newParameterId, 1);
        while (str_ends_with($newParameterId, "-")) $newParameterId = substr($newParameterId, 0, -1);

        $number = 1;
        if (does_parameter_exist($createSessionData, $newParameterId)) {
            while (does_parameter_exist($createSessionData, $newParameterId . "-$number")) {
                $number += 1;
            }

            $newParameterId = $newParameterId . "-$number";
        }

        $createSessionData['new_parameters'][$newParameterId] = [
            'id' => $newParameterId,
            'name' => $newParameterName
        ];

        $createSessionData['elements'] = array_filter($createSessionData['elements'], function ($element) {
            if ($element['type'] === 'new_parameter_input') return false;
            return true;
        });

        $createSessionData['elements'][$newParameterId] = [
            'type' => 'input_parameter',
            'id' => uniqid(),
            'name' => 'parameter_' . $newParameterId,
            'parameter_id' => $newParameterId,
            'parameter_name' => $newParameterName
        ];

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "remove_image") {
        $image = $_GET['image'] ?? null;

        if ($image === null) redirect_and_kill($thisUrl . "&render_without_layout=true");

        $createSessionData['images'] = array_filter($createSessionData['images'], fn($i) => $i !== $image);

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
    } else if ($action === "add_image") {
        $image = $_FILES['image'] ?? null;

        if ($image === null) redirect_and_kill($thisUrl . "&render_without_layout=true");

        if ($image['error'] !== 0) {
            validation_errors_add("image", "Wystąpił błąd podczas przesyłania pliku.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        if ($image['size'] > 1024 * 1024 * 5) {
            validation_errors_add("image", "Plik jest za duży. Maksymalny rozmiar pliku to 5MB.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        if (!in_array($image['type'], ["image/jpeg", "image/png"]) || !in_array(mime_content_type($image['tmp_name']), ['image/jpeg', 'image/png'])) {
            validation_errors_add("image", "Plik musi być w formacie JPG lub PNG.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $explodedImageName = explode(".", "$image[name]");
        $imageId = uniqid("product_image_") . "." . array_pop($explodedImageName);


        $imagePath = __DIR__ . "/../../images/$imageId";

        if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
            validation_errors_add("image", "Wystąpił błąd podczas przesyłania pliku.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $createSessionData['images'][] = $imageId;

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "choose_category") {
        $categoryId = $_POST['category_id'] ?? null;
        if ($categoryId === null) redirect_and_kill($thisUrl . "&render_without_layout=true");

        if (!in_array("choose_category", array_map(fn($e) => $e['type'], $createSessionData['elements']))) {
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        if ($categoryId === "*new_category*") {
            $createSessionData['elements'][] = ['type' => 'new_category_input'];
            $createSessionData['elements'] = array_filter($createSessionData['elements'], fn($e) => $e['type'] !== 'choose_category');
        } else if (in_array($categoryId, array_keys($createSessionData['new_categories']))) {
            old_input_add("category_id", $categoryId);
        } else {
            if (!is_numeric($categoryId)) redirect_and_kill($thisUrl . "&render_without_layout=true");

            $categoryId = intval($categoryId);

            $rawCategories = db_query_rows($db, "SELECT id FROM categories", []);
            $categoryIds = array_map(fn($category) => $category['id'], $rawCategories);

            if (!in_array($categoryId, $categoryIds)) redirect_and_kill($thisUrl . "&render_without_layout=true");

            old_input_add("category_id", $categoryId);
        }

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "confirm_new_category_name") {
        $categoryName = $_POST['new_category_name'] ?? null;
        if (!is_string($categoryName)) redirect_and_kill($thisUrl . "&render_without_layout=true");

        if (!in_array("new_category_input", array_map(fn($e) => $e['type'], $createSessionData['elements']))) {
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $categoryName = trim($categoryName);

        if (strlen($categoryName) < 3) {
            validation_errors_add("new_category_name", "Pole nazwa nowej kategorii musi mieć więcej niż 3 znaki.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        if (strlen($categoryName) > 64) {
            validation_errors_add("new_category_name", "Pole nazwa nowej kategorii musi mieć więcej niż 64 znaki.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $rawCategories = db_query_rows($db, "SELECT id, name FROM categories", []);

        $categoryNames = array_map(fn($category) => $category['name'], $rawCategories);
        if (in_array($categoryName, $categoryNames)) {
            validation_errors_add("new_category_name", "Kategoria o takiej nazwie już istnieje.");
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $createSessionData['new_categories'][uniqid()] = $categoryName;

        $createSessionData['elements'] = array_filter($createSessionData['elements'], fn($e) => $e['type'] !== 'new_category_input');

        $categories = array_map(fn($category) => [
            'text' => $category['name'],
            'value' => $category['id']
        ], $rawCategories);

        foreach ($createSessionData['new_categories'] as $key => $category) {
            $categories[] = ['text' => $category, 'value' => $key];
        }

        $categories[] = [
            'text' => 'Nowa kategoria',
            'value' => '*new_category*'
        ];

        $createSessionData['elements'][] = [
            'type' => 'choose_category',
            'options' => $categories
        ];

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "resign_new_category_name") {
        if (!in_array("new_category_input", array_map(fn($e) => $e['type'], $createSessionData['elements']))) {
            redirect_and_kill($thisUrl . "&render_without_layout=true");
        }

        $createSessionData['elements'] = array_filter($createSessionData['elements'], fn($e) => $e['type'] !== 'new_category_input');

        $categories = array_map(fn($category) => [
            'text' => $category['name'],
            'value' => $category['id']
        ], db_query_rows($db, "SELECT id, name FROM categories", []));

        foreach ($createSessionData['new_categories'] as $key => $category) {
            $categories[] = ['text' => $category, 'value' => $key];
        }

        $categories[] = [
            'text' => 'Nowa kategoria',
            'value' => '*new_category*'
        ];

        $createSessionData['elements'][] = [
            'type' => 'choose_category',
            'options' => $categories
        ];

        session_set_ttl($sessionVariableName, $createSessionData, 60 * 30);
        redirect_and_kill($thisUrl . "&render_without_layout=true");
    } else if ($action === "submit") {
        $name = $_POST['name'] ?? null;
        $description = $_POST['description'] ?? null;
        $price = $_POST['price'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;

        if (empty($name)) validation_errors_add("name", "Pole nazwa jest wymagane.");
        if (empty($description)) validation_errors_add("description", "Pole opis jest wymagane.");
        if (!is_numeric($price)) validation_errors_add("price", "Pole cena jest wymagane.");
        if (empty($categoryId)) validation_errors_add("category_id", "Pole kategoria jest wymagane.");

        if (!validation_errors_is_empty()) redirect_and_kill($thisUrl);

        $name = trim($name);
        $description = trim($description);
        $price = trim($price);

        if (strlen($name) < 3) {
            validation_errors_add("name", "Pole nazwa musi mieć więcej niż 3 znaki.");
            redirect_and_kill($thisUrl);
        }

        if (strlen($name) > 64) {
            validation_errors_add("name", "Pole nazwa musi mieć mniej niż 64 znaki.");
            redirect_and_kill($thisUrl);
        }

        if (strlen($description) < 3) {
            validation_errors_add("description", "Pole opis musi mieć więcej niż 3 znaki.");
            redirect_and_kill($thisUrl);
        }

        if (strlen($description) > 1024) {
            validation_errors_add("description", "Pole opis musi mieć mniej niż 1024 znaki.");
            redirect_and_kill($thisUrl);
        }

        if (!validation_errors_is_empty()) redirect_and_kill($thisUrl);

        $price = floatval($price);

        $categories = db_query_rows($db, "SELECT id FROM categories", []);

        if (in_array($categoryId, array_keys($createSessionData['new_categories']))) {
            $categoryId = $createSessionData['new_categories'][$categoryId];
        } else if (!in_array($categoryId, array_map(fn($category) => $category['id'], $categories))) {
            validation_errors_add("category_id", "Pole kategoria jest wymagane.");
        } else {
            $categoryId = intval($categoryId);
        }

        if (!validation_errors_is_empty()) {
            redirect_and_kill($thisUrl);
        }

        $parameterNames = array_filter(array_keys($_POST), fn($parameter) => str_starts_with($parameter, "parameter_"));
        $parameterNames = array_map(fn($parameter) => substr($parameter, strlen("parameter_")), $parameterNames);
        $parameterNames = array_filter($parameterNames, fn($name) => does_parameter_exist($createSessionData, $name));

        $parameters = [];
        foreach ($parameterNames as $parameterName) {
            $parameters[$parameterName] = trim($_POST["parameter_" . $parameterName]);
            if ($parameters[$parameterName] === "") {
                validation_errors_add("parameter_" . $parameterName, "Pole jest wymagane.");
                redirect_and_kill($thisUrl . "&render_without_layout=true");
            }
        }

        db_transaction(function (mysqli $db) use ($name, $description, $price, $categoryId, $parameters, $createSessionData) {
            if (gettype($categoryId) === "string") {
                $stmt = db_execute_stmt($db, "INSERT INTO categories (name) VALUES (?)", [$categoryId]);
                $categoryId = $stmt->insert_id;
                $stmt->close();
            }

            $stmt = db_execute_stmt($db, "INSERT INTO products (name, description, category_id, price) VALUES (?, ?, ?, ?)", [
                $name, $description, $price, $categoryId
            ]);
            $id = $stmt->insert_id;
            $stmt->close();

            if (count($parameters) > 0) {
                $queryParts = array_map(fn($parameterId) => "(?, ?, ?)", $parameters);
                $queryParts = join(', ', $queryParts);

                $queryValues = [];
                foreach ($parameters as $parameterId => $parameterValue) {
                    $queryValues[] = $id;
                    $queryValues[] = $parameterId;
                    $queryValues[] = $parameterValue;

                    if (isset($createSessionData['new_parameters'][$parameterId])) {
                        db_execute_stmt($db, "INSERT INTO parameters (id, name) VALUES (?, ?)", [$parameterId, $createSessionData['new_parameters'][$parameterId]['name']]);
                    }
                }

                db_execute_stmt($db, "INSERT INTO products_have_parameters (product_id, parameter_id, value) VALUES " . $queryParts, $queryValues);
            }

            foreach ($createSessionData['images'] as $image) {
                db_execute_stmt($db, "INSERT INTO products_images (product_id, image) VALUES (?, ?)", [$id, $image]);
            }

            clear_unused_categories($db);
            clear_unused_images($db);
            clear_unused_parameters($db);
        });

        session_flash("after_product_creation", true);
        session_remove($createSessionId);
        redirect_and_kill($thisUrl);
    }
}

ob_start(); ?>
    <form method="POST" action="<?= $thisUrl ?>&action=submit"
          id="edit-product-form"
          class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Tworzenie produktu</h1>

        <div class="w-full overflow-x-auto flex flex-row items-center gap-4">
            <?php foreach ($createSessionData['images'] as $productImage): ?>
                <div style="background-image: url('<?= config("app.url") ?>/images/<?= $productImage ?>');"
                     hx-post="<?= $thisUrl ?>&action=remove_image&image=<?= $productImage ?>"
                     hx-include="form"
                     hx-target="form"
                     hx-swap="outerHTML"
                     hx-trigger="click"
                     class="cursor-pointer w-full aspect-square rounded-xl h-24 w-24 bg-no-repeat bg-cover relative product-photo">
                    <div class="bg-neutral-800 bg-opacity-80 top-0 left-0 w-full h-full text-neutral-200 flex justify-center items-center">
                        <div class="h-10 w-10">
                            <?= file_get_contents(__DIR__ . "/../../assets/trash-icon.svg") ?>
                        </div>
                    </div>

                    <input name="images[]" value="<?= htmlspecialchars($productImage) ?>" type="hidden"/>
                </div>
            <?php endforeach; ?>

            <div class="h-24 w-24 flex justify-center items-center text-neutral-200 border-2 border-neutral-200 rounded-xl relative">
                <input type="file" name="image" class="opacity-0 absolute top-0 left-0 w-full h-full"
                       hx-post="<?= $thisUrl ?>&action=add_image" hx-encoding="multipart/form-data"
                       hx-include="form" hx-target="form" hx-swap="outerHTML" hx-trigger="change"/>
                <div class="h-6 w-6">
                    <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                </div>
            </div>
        </div>
        <?php if (validation_errors_has("image")): ?>
            <span class="text-red-400 font-bold"><?= htmlspecialchars(validation_errors_get("image")) ?></span>
        <?php endif; ?>

        <div class="flex flex-col gap-4" id="edit-product-form-body">
            <?= render_textfield(label: 'Nazwa', name: 'name', type: 'text') ?>
            <?= render_textfield(label: 'Opis', name: 'description', type: 'textarea') ?>
            <?= render_textfield(label: 'Cena', name: 'price', type: 'number') ?>

            <?php foreach ($createSessionData['elements'] as $element): ?>
                <?php if ($element['type'] === 'choose_parameter'): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_select("Wybierz parametr", "choose_parameter_" . $element['id'], options: $element['options'], id: $element['id']) ?>

                        <div hx-post="<?= $thisUrl ?>&action=confirm_choose_parameter"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                            </div>
                        </div>

                        <div hx-post="<?= $thisUrl ?>&action=resign_choose_parameter"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($element['type'] === "input_parameter"): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_textfield($element['parameter_name'], $element['name'], id: $element['id']) ?>

                        <div hx-post="<?= $thisUrl ?>&action=remove_parameter&parameter_id=<?= $element['parameter_id'] ?>"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($element['type'] === "new_parameter_input"): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_textfield("Podaj nazwę nowego parametru", 'new_parameter_name', oldInput: false) ?>

                        <div hx-post="<?= $thisUrl ?>&action=confirm_new_parameter"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                            </div>
                        </div>

                        <div hx-post="<?= $thisUrl ?>&action=remove_parameter&parameter_id=<?= urlencode("*new_parameter*") ?>"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($element['type'] === 'choose_category'): ?>
                    <?= render_select("Wybierz kategorię", 'category_id', options: $element['options'], attributes: [
                        'hx-post' => $thisUrl . "&action=choose_category",
                        'hx-include' => 'form',
                        'hx-target' => 'form',
                        'hx-swap' => 'outerHTML',
                        'hx-trigger' => 'change'
                    ]) ?>
                <?php elseif ($element['type'] === "new_category_input"): ?>
                    <div class="flex flex-row gap-4 items-end">
                        <?= render_textfield(label: "Nazwa nowej kategorii", name: "new_category_name") ?>

                        <div hx-post="<?= $thisUrl ?>&action=confirm_new_category_name"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                            </div>
                        </div>

                        <div hx-post="<?= $thisUrl ?>&action=resign_new_category_name"
                             hx-include="form" hx-target="form" hx-swap="outerHTML"
                             class="flex items-end p-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer rounded-xl">
                            <div class="h-6 w-6">
                                <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!in_array("choose_parameter", array_map(fn($e) => $e['type'], $createSessionData['elements'])) &&
                !is_new_parameter_name_input_in_edit_session($createSessionData)): ?>
                <div hx-post="<?= $thisUrl ?>&action=add_parameter"
                     hx-include="form"
                     hx-target="form"
                     hx-swap="outerHTML"
                     class="flex flex-row gap-4 text-neutral-200 bg-neutral-800 hover:bg-neutral-700 cursor-pointer p-4 rounded-xl">
                    <div class="h-6 w-6">
                        <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
                    </div>
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
        <style>
            .product-photo > div {
                visibility: hidden;
            }

            .product-photo:hover > div {
                visibility: visible;
            }
        </style>

        <div class="flex justify-center items-center p-4">
            <?= $formContent ?>
        </div>
    <?php });
} else {
    echo $formContent;
}