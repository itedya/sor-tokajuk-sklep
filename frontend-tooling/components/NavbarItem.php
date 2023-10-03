<?php

checkIfLoadedStraightfordwardly(__FILE__);

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