<?php

require_once __DIR__ . '/tooling/autoload.php';

function render_product_list(bool $withNavbarRender = false): string
{
    ob_start();
    foreach (cart_get_products() as $product): ?>
        <div class="py-4 flex flex-row gap-4 w-full">
            <div class="flex flex-col gap-2 flex-grow">
                <h3 class="text-xl text-neutral-300 font-bold"><?= $product['name'] ?></h3>
            </div>
            <div class="flex flex-col gap-2 items-end">
                <div class="text-neutral-200 text-xl">
                    <?= $product['price'] * $product['quantity'] ?> zł
                </div>

                <div class="flex flex-row divide-x divide-neutral-700 border border-neutral-700 text-neutral-200 rounded-xl">
                    <button hx-post="<?= base_url('/cart.php', ['action' => 'remove-from-cart', 'product_id' => $product['id']]) ?>"
                            hx-swap="innerHTML" hx-target="#cart-items" hx-include="#other-details-container"
                            class="flex-grow p-2 w-10 flex justify-center items-center text-neutral-400 cursor-pointer hover:text-neutral-300 duration-200">
                        <?= file_get_contents(__DIR__ . '/assets/chevron-left.svg') ?>
                    </button>

                    <div class="p-2 flex justify-center items-center text-neutral-200">
                        <?= $product['quantity'] ?>
                    </div>

                    <button hx-post="<?= base_url('/cart.php', ['action' => 'add-to-cart', 'product_id' => $product['id']]) ?>"
                            hx-swap="innerHTML" hx-target="#cart-items" hx-include="#other-details-container"
                            class="flex-grow w-10 flex justify-center items-center text-neutral-400 cursor-pointer hover:text-neutral-300 duration-200">
                        <?= file_get_contents(__DIR__ . '/assets/chevron-right.svg') ?>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="py-4 flex flex-row gap-4 w-full">
        <div class="flex flex-col gap-2 flex-grow">
            <h3 class="text-xl text-neutral-300 font-bold">Suma <span class="text-sm">(bez dostawy)</span></h3>
        </div>
        <div class="flex flex-col gap-2 items-end">
            <div class="text-neutral-200 text-xl">
                <?= cart_get_total() ?> zł
            </div>
        </div>
    </div>
    <?php if ($withNavbarRender): ?>
    <script>
        (() => {
            const form = document.querySelector('#other-details-container');
            const formData = new FormData(form);

            fetch('<?= base_url('/cart.php', ['action' => 'render-navbar']) ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    const navbar = document.querySelector('#navbar');
                    navbar.innerHTML = html;
                });

            fetch('<?= base_url('/cart.php', ['action' => 'render-other-details']) ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    const container = document.querySelector('#other-details-container');
                    container.innerHTML = html;
                });
        })();
    </script>
<?php
endif;
    return ob_get_clean();
}

function render_other_details(array $paymentMethods, array $deliveryMethods, ?array $addresses): string
{
    ob_start();
    ?>
    <form method="POST" action="<?= base_url('/cart.php', ['action' => 'buy']) ?>"
          class="flex flex-col w-full gap-8 pb-4"
          id="other-details-container">
        <?= render_select(label: 'Sposób płatności', name: 'payment_method', options: array_map(fn($method) => [
            'value' => strval($method['id']),
            'text' => $method['name']
        ], $paymentMethods)) ?>

        <?= render_select('Sposób dostawy', 'delivery_method', array_map(fn($method) => [
            'value' => strval($method['id']),
            'text' => $method['name'] . ' (' . $method['price'] . ' zł)'
        ], $deliveryMethods)) ?>

        <?php if ($addresses): ?>
            <h2 class="text-2xl font-bold text-neutral-200">Dane do wysyłki</h2>
        <input type="hidden"
               name="address_id" <?php if (old_input_has("address_id")): ?> value="<?= old_input_get("address_id") ?>" <?php endif; ?> />
        <?php if (validation_errors_has("address_id")): ?>
            <span><?= validation_errors_get("address_id") ?></span>
        <?php endif; ?>
            <div class="flex flex-row overflow-x-auto w-full gap-4">
                <?php foreach ($addresses as $address): ?>
                    <div class="flex flex-row p-4 border border-neutral-700 rounded-xl text-neutral-200  cursor-pointer"
                         data-address="<?= $address['id'] ?>">
                        <?= $address['first_line'] ?><br/>
                        <?= $address['second_line'] ?><br/>
                        <?= $address['city'] ?> <?= $address['postal_code'] ?>
                    </div>
                <?php endforeach; ?>

                <div class="text-neutral-200 flex flex-row p-4 border border-neutral-700 rounded-xl text-neutral-200 cursor-pointer justify-center items-center w-24"
                     onclick="window.location.href = `<?= base_url('/client-panel/index.php', ['panel' => 'addresses']) ?>`">
                    <div class="w-6 aspect-ratio">
                        <?= file_get_contents(__DIR__ . '/assets/plus-icon.svg') ?>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-neutral-200">Dane rozliczeniowe</h2>
        <input type="hidden"
               name="delivery_address_id"
            <?php if (old_input_has("delivery_address_id")): ?>
                value="<?= old_input_get("delivery_address_id") ?>"
            <?php endif; ?> />
        <?php if (validation_errors_has("delivery_address_id")): ?>
            <span><?= validation_errors_get("delivery_address_id") ?></span>
        <?php endif; ?>
            <div class="flex flex-row overflow-x-auto gap-4">
                <?php foreach ($addresses as $address): ?>
                    <div class="flex flex-row p-4 border border-neutral-700 rounded-xl text-neutral-200  cursor-pointer"
                         data-delivery-address="<?= $address['id'] ?>">
                        <?= $address['first_line'] ?><br/>
                        <?= $address['second_line'] ?><br/>
                        <?= $address['city'] ?> <?= $address['postal_code'] ?>
                    </div>
                <?php endforeach; ?>

                <div class="text-neutral-200 flex flex-row p-4 border border-neutral-700 rounded-xl text-neutral-200 cursor-pointer justify-center items-center w-24"
                     onclick="window.location.href = `<?= base_url('/client-panel/index.php', ['panel' => 'addresses']) ?>`">
                    <div class="w-6 aspect-ratio">
                        <?= file_get_contents(__DIR__ . '/assets/plus-icon.svg') ?>
                    </div>
                </div>
            </div>

            <script>
                ['delivery-address', 'address'].forEach((name) => {
                    document.querySelectorAll(`[data-${name}]`).forEach(element => {
                        element.addEventListener("click", (e) => {
                            document.querySelectorAll(`[data-${name}]`).forEach(ele => {
                                ele.classList.remove("border-blue-700");
                                ele.classList.add("border-neutral-700");
                            });

                            document.querySelector(`[name=${name.replace("-", "_")}_id]`).value = element.getAttribute(`data-${name}`);
                            element.classList.remove("border-neutral-700");
                            element.classList.add("border-blue-700");
                        });
                    });
                });
            </script>
        <?php else: ?>
            <h2 class="text-2xl font-bold text-neutral-200">Dane do wysyłki</h2>
            <div class="flex flex-col gap-4">
                <?= render_textfield(label: 'Pierwsza linia adresu', name: 'delivery_address_first_line') ?>
                <?= render_textfield(label: 'Druga linia adresu', name: 'delivery_address_second_line') ?>
                <?= render_textfield(label: 'Miasto', name: 'delivery_address_city') ?>
                <?= render_textfield(label: 'Kod pocztowy', name: 'delivery_address_postal_code') ?>
            </div>

            <h2 class="text-2xl font-bold text-neutral-200">Dane rozliczeniowe</h2>
            <div class="flex flex-col gap-4">
                <?php if (!auth_is_logged_in()): ?>
                    <?= render_textfield(label: 'Adres email', name: 'email') ?>
                <?php endif; ?>
                <?= render_textfield(label: 'Pierwsza linia adresu', name: 'address_first_line') ?>
                <?= render_textfield(label: 'Druga linia adresu', name: 'address_second_line') ?>
                <?= render_textfield(label: 'Miasto', name: 'address_city') ?>
                <?= render_textfield(label: 'Kod pocztowy', name: 'address_postal_code') ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-row justify-end items-center gap-4">
            <button class="px-8 py-2 bg-blue-700 hover:bg-blue-800 text-neutral-200 font-bold rounded-xl shadow-md transition-all duration-200">
                Zamów
            </button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}

$action = $_GET['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    foreach ($_POST as $key => $value) old_input_add($key, $value);

    if ($action === 'add-to-cart') {
        $productId = $_GET['product_id'] ?? null;
        if (!is_numeric($productId)) abort(400);
        $productId = intval($productId);


        $products = cart_get_products();
        $product = array_filter($products, fn($product) => $product['id'] === $productId);
        if (count($product) === 0) abort(400);

        cart_add_product($productId);
        echo render_product_list(withNavbarRender: true);
        return;
    } else if ($action === 'remove-from-cart') {
        $productId = $_GET['product_id'] ?? null;
        if (!is_numeric($productId)) abort(400);
        $productId = intval($productId);

        $products = cart_get_products();
        $product = array_filter($products, fn($product) => $product['id'] === $productId);
        if (count($product) === 0) abort(400);

        cart_remove_product($productId);
        echo render_product_list(withNavbarRender: true);
        return;
    } else if ($action === 'render-navbar') {
        echo render_navbar();
        return;
    } else if ($action === 'render-other-details') {
        if (count(cart_get_products()) === 0) return;
        $db = get_db_connection();

        $paymentMethods = database_payment_types_get($db);
        $deliveryMethods = database_delivery_methods_get($db);
        $address = null;
        if (auth_is_logged_in()) $address = database_addresses_get_by_user_id($db, auth_get_user_id());

        $db->close();

        echo render_other_details($paymentMethods, $deliveryMethods, $address);
        return;
    } else if ($action === 'buy') {
        function validate_address_item(mixed $value, bool $minCheck): ?string
        {
            if ($minCheck) {
                if (empty($value)) return 'Pole jest wymagane';
            }
            if (!is_string($value)) return 'Pole musi być ciągiem znaków';
            if ($minCheck) {
                if (strlen($value) < 3) return 'Pole musi zawierać co najmniej 3 znaki';
            }
            if (strlen($value) > 255) return 'Pole może zawierać maksymalnie 255 znaków';
            return null;
        }

        function validate_new_address(): void
        {
            if (($error = validate_address_item($_POST['delivery_address_first_line'] ?? '', true)) !== null) {
                validation_errors_add('delivery_address_first_line', $error);
            }

            if (($error = validate_address_item($_POST['delivery_address_second_line'] ?? '', false)) !== null) {
                validation_errors_add('delivery_address_second_line', $error);
            }

            if (($error = validate_address_item($_POST['delivery_address_city'] ?? '', true)) !== null) {
                validation_errors_add('delivery_address_city', $error);
            }

            if (($error = validate_address_item($_POST['delivery_address_postal_code'] ?? '', true)) !== null) {
                validation_errors_add('delivery_address_postal_code', $error);
            }

            if (($error = validate_address_item($_POST['address_first_line'] ?? '', true)) !== null) {
                validation_errors_add('address_first_line', $error);
            }

            if (($error = validate_address_item($_POST['address_second_line'] ?? '', false)) !== null) {
                validation_errors_add('address_second_line', $error);
            }

            if (($error = validate_address_item($_POST['address_city'] ?? '', true)) !== null) {
                validation_errors_add('address_city', $error);
            }

            if (($error = validate_address_item($_POST['address_postal_code'] ?? '', true)) !== null) {
                validation_errors_add('address_postal_code', $error);
            }
        }

        if (!auth_is_logged_in()) {
            if (($error = validate_email($_POST['email'] ?? '')) !== null) {
                validation_errors_add('email', $error);
            }

            validate_new_address();

            $paymentMethodId = $_POST['payment_method'] ?? null;
            if (!is_numeric($paymentMethodId)) validation_errors_add('payment_method', 'Nieprawidłowa wartość');
            $paymentMethodId = intval($paymentMethodId);

            $deliveryMethodId = $_POST['delivery_method'] ?? null;
            if (!is_numeric($deliveryMethodId)) validation_errors_add('delivery_method', 'Nieprawidłowa wartość');
            $deliveryMethodId = intval($deliveryMethodId);

            if (!validation_errors_is_empty()) redirect_and_kill(base_url('/cart.php'));

            $address = [
                'first_line' => trim($_POST['address_first_line']),
                'second_line' => trim($_POST['address_second_line'] ?? ''),
                'city' => trim($_POST['address_city']),
                'postal_code' => trim($_POST['address_postal_code'])
            ];

            $deliveryAddress = [
                'first_line' => trim($_POST['delivery_address_first_line']),
                'second_line' => trim($_POST['delivery_address_second_line'] ?? ''),
                'city' => trim($_POST['delivery_address_city']),
                'postal_code' => trim($_POST['delivery_address_postal_code'])
            ];

            db_transaction(function ($db) use (&$orderId, $address, $deliveryAddress) {
                $paymentType = database_payment_types_get_by_id($db, $_POST['payment_method']);
                if ($paymentType === null) validation_errors_add('payment_method', 'Nieprawidłowa wartość');

                $deliveryMethod = database_delivery_methods_get_by_id($db, $_POST['delivery_method']);
                if ($deliveryMethod === null) validation_errors_add('delivery_method', 'Nieprawidłowa wartość');

                if (!validation_errors_is_empty()) redirect_and_kill(base_url('/cart.php'));

                $deliveryAddressId = database_addresses_create(
                    db: $db,
                    userId: null,
                    firstLine: $address['first_line'],
                    secondLine: $address['second_line'],
                    city: $address['city'],
                    postalCode: $address['postal_code']
                );

                $addressId = database_addresses_create(
                    db: $db,
                    userId: null,
                    firstLine: $address['first_line'],
                    secondLine: $address['second_line'],
                    city: $address['city'],
                    postalCode: $address['postal_code']
                );

                $orderId = database_orders_create(
                    db: $db, userId: null,
                    deliveryMethodId: $deliveryMethod['id'],
                    paymentTypeId: $paymentType['id'],
                    deliveryAddressId: $deliveryAddressId,
                    addressId: $addressId
                );

                $products = cart_get_products();

                foreach ($products as $product) {
                    database_order_details_create(
                        db: $db,
                        orderId: $orderId,
                        productId: $product['id'],
                        quantity: $product['quantity']
                    );
                }
            });

            sendMail($_POST['email'],
                'Zamówienie',
                sprintf(
                    'Zamówienie zostało złożone. Możesz je śledzić pod linkiem: <a href="%s">%s</a>',
                    base_url('/orders/details.php', ['id' => $orderId]),
                    base_url('/orders/details.php', ['id' => $orderId])
                ));

            cart_empty();
            redirect_and_kill(base_url('/orders/details.php', ['id' => $orderId]));
        } else {
            db_transaction(function ($db) use (&$orderId, &$user) {
                $addresses = database_addresses_get_by_user_id($db, auth_get_user_id());
                if (!$addresses) {
                    validate_new_address();

                    if (!validation_errors_is_empty()) redirect_and_kill(base_url('/cart.php'));

                    $address = [
                        'first_line' => trim($_POST['address_first_line']),
                        'second_line' => trim($_POST['address_second_line'] ?? ''),
                        'city' => trim($_POST['address_city']),
                        'postal_code' => trim($_POST['address_postal_code'])
                    ];

                    $deliveryAddress = [
                        'first_line' => trim($_POST['delivery_address_first_line']),
                        'second_line' => trim($_POST['delivery_address_second_line'] ?? ''),
                        'city' => trim($_POST['delivery_address_city']),
                        'postal_code' => trim($_POST['delivery_address_postal_code'])
                    ];
                } else {
                    if (!is_numeric($_POST["delivery_address_id"] ?? null)) {
                        validation_errors_add("delivery_address_id", "Pole jest wymagane (kliknij w adres).");
                        redirect_and_kill(base_url('/cart.php'));
                    }

                    $deliveryAddress = database_addresses_get_by_id($db, intval($_POST["delivery_address_id"]));
                    if ($deliveryAddress === null) {
                        validation_errors_add("delivery_address_id", "Adres który wybrałeś jest niepoprawny.");
                        redirect_and_kill(base_url('/cart.php'));
                    }

                    if ($deliveryAddress['user_id'] !== auth_get_user_id()) {
                        validation_errors_add("delivery_address_id", "Adres który wybrałeś nie jest twój.");
                        redirect_and_kill(base_url('/cart.php'));
                    }

                    if (!is_numeric($_POST["address_id"] ?? null)) {
                        validation_errors_add("address_id", "Pole jest wymagane (kliknij w adres).");
                        redirect_and_kill(base_url('/cart.php'));
                    }

                    $address = database_addresses_get_by_id($db, intval($_POST["address_id"]));
                    if ($address === null) {
                        validation_errors_add("address_id", "Adres który wybrałeś jest niepoprawny.");
                        redirect_and_kill(base_url('/cart.php'));
                    }

                    if ($address['user_id'] !== auth_get_user_id()) {
                        validation_errors_add("address_id", "Adres który wybrałeś nie jest twój.");
                        redirect_and_kill(base_url('/cart.php'));
                    }
                }

                $paymentMethodId = $_POST['payment_method'] ?? null;
                if (!is_numeric($paymentMethodId)) validation_errors_add('payment_method', 'Nieprawidłowa wartość');
                $paymentMethodId = intval($paymentMethodId);

                $deliveryMethodId = $_POST['delivery_method'] ?? null;
                if (!is_numeric($deliveryMethodId)) validation_errors_add('delivery_method', 'Nieprawidłowa wartość');
                $deliveryMethodId = intval($deliveryMethodId);

                if (!validation_errors_is_empty()) redirect_and_kill(base_url('/cart.php'));

                if (database_payment_types_get_by_id($db, $paymentMethodId) === null) {
                    validation_errors_add("payment_method", "Ta metoda płatności nie istnieje.");
                }

                $deliveryMethod = database_delivery_methods_get_by_id($db, $deliveryMethodId);
                if ($deliveryMethod === null) validation_errors_add("delivery_method", "Ta metoda dostawy nie istnieje.");

                if (!validation_errors_is_empty()) redirect_and_kill(base_url('/cart.php'));

                if (!isset($address['id'])) {
                    $address['id'] = database_addresses_create(
                        db: $db,
                        userId: auth_get_user_id(),
                        firstLine: $address['first_line'],
                        secondLine: $address['second_line'],
                        city: $address['city'],
                        postalCode: $address['postal_code']
                    );

                    if ($address['first_line'] !== $deliveryAddress['first_line'] ||
                        $address['second_line'] !== $deliveryAddress['second_line'] ||
                        $address['postal_code'] !== $deliveryAddress['postal_code'] ||
                        $address['city'] !== $deliveryAddress['city']) {

                        $deliveryAddress['id'] = database_addresses_create(
                            db: $db,
                            userId: auth_get_user_id(),
                            firstLine: $deliveryAddress['first_line'],
                            secondLine: $deliveryAddress['second_line'],
                            city: $deliveryAddress['city'],
                            postalCode: $deliveryAddress['postal_code']
                        );
                    } else {
                        $deliveryAddress['id'] = $address['id'];
                    }
                }

                $orderId = database_orders_create(
                    db: $db, userId: auth_get_user_id(),
                    deliveryMethodId: $deliveryMethod['id'],
                    paymentTypeId: $paymentMethodId,
                    deliveryAddressId: $deliveryAddress['id'],
                    addressId: $address['id']
                );

                $products = cart_get_products();

                foreach ($products as $product) {
                    database_order_details_create(
                        db: $db,
                        orderId: $orderId,
                        productId: $product['id'],
                        quantity: $product['quantity']
                    );
                }

                $user = database_users_get_by_id($db, auth_get_user_id());
            });


            sendMail($user['email'],
                'Zamówienie',
                sprintf(
                    'Zamówienie zostało złożone. Możesz je śledzić pod linkiem: <a href="%s">%s</a>',
                    base_url('/orders/details.php', ['id' => $orderId]),
                    base_url('/orders/details.php', ['id' => $orderId])
                ));

            cart_empty();
            redirect_and_kill(base_url('/orders/details.php', ['id' => $orderId]));
        }
    }
}

$db = get_db_connection();

$paymentMethods = database_payment_types_get($db);
$deliveryMethods = database_delivery_methods_get($db);

$address = null;
if (auth_is_logged_in()) $address = database_addresses_get_by_user_id($db, auth_get_user_id());

$db->close();

echo render_in_layout(function () use ($paymentMethods, $deliveryMethods, $address) { ?>
    <div class="w-full max-w-3xl mx-auto flex flex-col gap-4 px-4 justify-center">
        <h2 class="text-3xl text-neutral-300 font-bold">Koszyk</h2>

        <div class="flex flex-col w-full divide-y divide-neutral-700" id="cart-items">
            <?= render_product_list() ?>
        </div>

        <?php if (count(cart_get_products()) > 0): ?>
            <?= render_other_details($paymentMethods, $deliveryMethods, $address) ?>
        <?php endif; ?>
    </div>
<?php });