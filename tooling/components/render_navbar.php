<?php

function render_navbar()
{
    $elements = [];

    if (auth_is_logged_in()) {
        $elements = [
            ['href' => '/', 'text' => 'Strona główna'],
            ['href' => '/client-panel/index.php', 'text' => 'Panel klienta'],
            ['dropdown' => true, 'name' => 'Zarządzanie', 'items' => [
                ['href' => '/management/products.php', 'text' => 'Zarządzaj produktami'],
                ['href' => '/management/payment-types.php', 'text' => 'Zarządzaj sposobami płatności'],
                ['href' => '/management/delivery-methods.php', 'text' => 'Zarządzaj sposobami dostawy'],
                ['href' => '/management/additional-pages.php', 'text' => 'Zarządzaj dodatkowymi stronami'],
                ['href' => '/management/users.php', 'text' => 'Zarządzaj użytkownikami'],
            ]],
            ['href' => '/auth/logout.php', 'text' => 'Wyloguj się'],
        ];
    } else {
        $elements = [
            ['href' => '/', 'text' => 'Strona główna'],
            ['href' => '/auth/login.php', 'text' => 'Logowanie'],
            ['href' => '/auth/register.php', 'text' => 'Rejestracja'],
        ];
    }

    $db = get_db_connection();

    foreach (database_additional_pages_get($db) as $page) {
        $elements[] = [
            'href' => base_url('/additional-page.php', ['id' => $page['id']]),
            'text' => $page['name']
        ];
    }


    $db->close();

    ob_start();
    ?>
    <div id="navbar">
        <div class="fixed top-0 left-0 w-full bg-zinc-900 p-4 xl:p-0 border-b border-zinc-700 shadow h-20 md:h-auto flex justify-center items-center z-40">
            <div class="container flex flex-row justify-between items-center h-full px-8">
                <a href="<?= base_url('/') ?>" class="text-3xl text-zinc-300 font-bold">TrumniX</a>

                <div class="hidden xl:flex flex-row justify-center items-center">
                    <?php foreach ($elements as $element): ?>
                        <?php if (($element['dropdown'] ?? false) === true): ?>
                            <div class="text-sm text-neutral-300 px-2 py-4 relative" data-dropdown>
                                <button data-dropdown-button><?= htmlspecialchars($element['name']) ?></button>
                                <div class="absolute top-12 -right-2 divide-y divide-zinc-700 border border-zinc-700 rounded-xl shadow-lg bg-zinc-900 invisible min-w-5xl"
                                     data-dropdown-links>
                                    <?php foreach ($element['items'] as $dropdownElement): ?>
                                        <a class="block text-sm text-neutral-300 whitespace-nowrap p-2"
                                           href="<?= htmlspecialchars($dropdownElement['href']) ?>">
                                            <?= htmlspecialchars($dropdownElement['text']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <a class="text-sm text-neutral-300 px-2 py-4 h-full"
                               href="<?= htmlspecialchars($element['href']) ?>">
                                <?= htmlspecialchars($element['text']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <a href="<?= base_url('cart.php') ?>" class="w-6 aspect-square text-neutral-200 mx-4 relative cursor-pointer">
                        <?= file_get_contents(__DIR__ . '/../../assets/shopping-bag.svg') ?>

                        <?php if (cart_get_count() !== 0): ?>
                            <div class="bg-blue-600 rounded-xl text-sm p-0.5 text-center absolute -right-1/4 -top-1/4"><?= cart_get_count() ?></div>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="xl:hidden flex flex-row gap-4 justify-center items-center">
                    <a href="<?= base_url('cart.php') ?>" class="w-6 aspect-square text-neutral-200 mx-4 relative cursor-pointer">
                        <?= file_get_contents(__DIR__ . '/../../assets/shopping-bag.svg') ?>

                        <?php if (cart_get_count() !== 0): ?>
                            <div class="bg-blue-600 rounded-xl text-sm p-0.5 text-center absolute -right-1/4 -top-1/4"><?= cart_get_count() ?></div>
                        <?php endif; ?>
                    </a>

                    <button class="p-2 text-zinc-100" id="navbarItemsTrigger">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="xl:hidden">
            <div class="fixed top-0 left-0 z-30 bg-zinc-900 mt-20 flex flex-col w-full hidden" id="navbarItems">
                <?php foreach ($elements as $element): ?>
                    <?php if (($element['dropdown'] ?? false) === true): ?>
                        <?php foreach ($element['items'] as $dropdownElement): ?>
                            <a class="text-xl text-neutral-300 border-b border-zinc-700 w-full p-4 hover:bg-zinc-800"
                               href="<?= htmlspecialchars($dropdownElement['href']) ?>">
                                <?= htmlspecialchars($dropdownElement['text']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a class="text-xl text-neutral-300 border-b border-zinc-700 w-full p-4 hover:bg-zinc-800"
                           href="<?= htmlspecialchars($element['href']) ?>">
                            <?= htmlspecialchars($element['text']) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            const triggerMobileNavbar = () => {
                const navbarItemsElement = document.querySelector("#navbarItems");

                navbarItemsElement.classList.toggle("hidden");
            }

            const navbarItemsTrigger = document.querySelector("#navbarItemsTrigger");
            navbarItemsTrigger.addEventListener("click", triggerMobileNavbar);

            document.addEventListener("click", e => {
                const isDropdownButton = e.target.matches("[data-dropdown-button]")
                if (!isDropdownButton && e.target.closest("[data-dropdown]") != null) return

                let currentDropdown
                if (isDropdownButton) {
                    currentDropdown = e.target.parentNode.querySelector("[data-dropdown-links]")
                    currentDropdown.classList.toggle("active")
                    currentDropdown.classList.toggle("invisible")
                }

                document.querySelectorAll("[data-dropdown-links].active").forEach(dropdown => {
                    if (dropdown === currentDropdown) return
                    dropdown.classList.add("invisible")
                })
            })
        </script>
    </div>
    <?php

    return ob_get_clean();
}
