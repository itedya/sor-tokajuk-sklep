<?php

function render_navbar()
{
    $elements = [];

    if (auth_is_logged_in()) {
        $elements = [
            ['href' => '/', 'text' => 'Strona główna'],
            ['href' => '/management/products.php', 'text' => 'Zarządzaj produktami'],
            ['href' => '/auth/logout.php', 'text' => 'Wyloguj się'],
        ];
    } else {
        $elements = [
            ['href' => '/', 'text' => 'Strona główna'],
            ['href' => '/auth/login.php', 'text' => 'Logowanie'],
            ['href' => '/auth/register.php', 'text' => 'Rejestracja'],
        ];
    }

    ob_start();
    ?>
    <div>
        <div class="fixed top-0 left-0 w-full bg-zinc-900 p-4 xl:p-0 border-b border-zinc-700 shadow h-20 md:h-auto flex justify-center items-center z-40">
            <div class="container flex flex-row justify-between items-center h-full px-8">
                <h1 class="text-3xl text-zinc-300 font-bold">TrumniX</h1>

                <div class="hidden xl:flex flex-row">
                    <?php foreach ($elements as $element): ?>
                        <a class="text-xl text-neutral-300 p-8 h-full" href="<?= htmlspecialchars($element['href']) ?>">
                            <?= htmlspecialchars($element['text']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <button class="p-2 text-zinc-100 xl:hidden" id="navbarItemsTrigger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="xl:hidden">
            <div class="fixed top-0 left-0 z-30 bg-zinc-900 mt-20 flex flex-col w-full hidden" id="navbarItems">
                <?php foreach ($elements as $element): ?>
                    <a class="text-xl text-neutral-300 border-b border-zinc-700 w-full p-4 hover:bg-zinc-800"
                       href="<?= htmlspecialchars($element['href']) ?>">
                        <?= htmlspecialchars($element['text']) ?>
                    </a>
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
        </script>
    </div>
    <?php

    return ob_get_clean();
}