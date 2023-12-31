<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_GET['id'] ?? null;
    if ($id === null) redirect_and_kill($_SERVER['REQUEST_URI']);

    $db = get_db_connection();

    $address = database_addresses_get_by_id($db, $id);
    if ($address === null) redirect_and_kill($_SERVER['REQUEST_URI']);
    if ($address['user_id'] !== auth_get_user_id()) redirect_and_kill($_SERVER['REQUEST_URI']);

    database_addresses_delete_by_id($db, $id);

    redirect_and_kill($_SERVER['REQUEST_URI']);
}

$db = get_db_connection();

$addresses = database_addresses_get_by_user_id($db, auth_get_user_id());

ob_start(); ?>
    <div class="w-full flex flex-col gap-8">
        <div class="flex flex-col gap-2 text-center lg:col-span-2">
            <h2 class="text-3xl font-bold">Adresy</h2>
        </div>

        <div class="flex flex-col gap-2 overflow-x-auto w-full">
            <div class="flex flex-row justify-end items-center w-full">
                <button hx-get="<?= htmlspecialchars(base_url("/client-panel/create_address.php")) ?>"
                        hx-trigger="click" hx-swap="innerHTML" hx-target="#swappable-panel"
                        class="px-8 py-2 rounded-xl text-neutral-200 bg-green-600 font-semibold">
                    Dodaj
                </button>
            </div>

            <?php
            $addressesHtml = render_table(
                ["Adres", "Miasto", "Kod pocztowy", "", ""],
                array_map(function ($row) {

                    $editButton = sprintf('<button hx-get="%s" hx-trigger="click" hx-swap="innerHTML" hx-target="#swappable-panel" class="%s">Edytuj</a>',
                        htmlspecialchars(base_url("/client-panel/edit_address.php", [
                            'id' => $row['id']
                        ])),
                        "px-4 py-2 bg-neutral-600 text-neutral-200 font-semibold rounded-lg text-center");

                    $deleteButton = sprintf('<button hx-post="%s" hx-trigger="click" hx-swap="innerHTML" hx-target="#swappable-panel" class="%s">Usuń</a>',
                        htmlspecialchars(base_url("/client-panel/addresses.php", [
                            'id' => $row['id']
                        ])),
                        "px-4 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg text-center");

                    return [
                        ['value' => $row['first_line'] . "<br />" . $row['second_line'], 'is_html' => true],
                        ['value' => $row['city']],
                        ['value' => $row['postal_code']],
                        ['value' => $editButton, 'is_html' => true],
                        ['value' => $deleteButton, 'is_html' => true],
                    ];
                }, $addresses)
            );

            if (count($addresses) === 0) {
                $addressesHtml = '<p class="text-xl text-center">Nie masz żadnych adresów</p>';
            }

            echo $addressesHtml;
            ?>
        </div>
    </div>
<?php
echo ob_get_clean();