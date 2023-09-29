<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "Navbar.php")) {
    http_response_code(404);
    die();
}

class NavbarItem implements Component
{
    public function __construct(
        private string $text,
        private string $href,
    ) {
    }

    public function render()
    {
        return <<<HTML
            <a class="text-xl text-neutral-300 p-8 h-full" href="{$this->href}">{$this->text}</a>
        HTML;
    }
}