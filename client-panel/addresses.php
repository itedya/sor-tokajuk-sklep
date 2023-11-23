<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // ...
}

$db = get_db_connection();

$addresses = database_addresses_get_by_user_id($db, auth_get_user_id());

ob_start(); ?>
    <div class="w-full flex flex-col gap-8">
        <div class="flex flex-col gap-2 text-center lg:col-span-2">
            <h2 class="text-3xl font-bold">Adresy</h2>
        </div>

        <div class="flex flex-col gap-2 overflow-x-auto w-full">
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