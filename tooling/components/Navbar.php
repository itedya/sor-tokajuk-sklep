<?php

class Navbar implements Component
{
    private $notLoggedInElements = [
        ['href' => '/', 'text' => 'Strona główna'],
        ['href' => '/auth/login.php', 'text' => 'Logowanie'],
        ['href' => '/auth/register.php', 'text' => 'Rejestracja'],
    ];

    private $loggedInElements = [
        ['href' => '/', 'text' => 'Strona główna'],
        ['href' => '/auth/logout.php', 'text' => 'Wyloguj się'],
    ];

    private function getNavbarElements()
    {
        $result = "";

        if (AuthorizationFacade::isAuthorized()) {
            foreach ($this->loggedInElements as $element) {
                $result .= (new NavbarItem($element['text'], $element['href']))->render();
            }
        } else {
            foreach ($this->notLoggedInElements as $element) {
                $result .= (new NavbarItem($element['text'], $element['href']))->render();
            }
        }

        return $result;
    }

    private function getMobileNavbarElements()
    {
        $result = "";

        if (AuthorizationFacade::isAuthorized()) {
            foreach ($this->loggedInElements as $element) {
                $result .= (new MobileNavbarItem($element['text'], $element['href']))->render();
            }
        } else {
            foreach ($this->notLoggedInElements as $element) {
                $result .= (new MobileNavbarItem($element['text'], $element['href']))->render();
            }
        }

        return $result;
    }

    public function render()
    {
        return <<<HTML
        <div>
            <div class="fixed top-0 left-0 w-full bg-zinc-900 p-4 md:p-0 border-b border-zinc-700 shadow h-20 md:h-auto flex justify-center items-center z-40">
                <div class="container flex flex-row justify-between items-center h-full px-8">
                    <h1 class="text-3xl text-zinc-300 font-bold">Sklep</h1>
            
                    <div class="hidden md:flex flex-row">
                        {$this->getNavbarElements()}
                    </div>
            
                    <button class="p-2 text-zinc-100 md:hidden" id="navbarItemsTrigger">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="md:hidden">
                <div class="fixed top-0 left-0 z-30 bg-zinc-900 mt-20 flex flex-col w-full hidden" id="navbarItems">
                    {$this->getMobileNavbarElements()}
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
        HTML;
    }
}