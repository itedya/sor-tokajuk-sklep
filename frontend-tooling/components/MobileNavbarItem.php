<?php

checkIfLoadedStraightfordwardly(__FILE__);

class MobileNavbarItem implements Component
{
    public function __construct(
        private string $text,
        private string $href,
    ) {
    }

    public function render()
    {
        return <<<HTML
            <a class="text-xl text-neutral-300 border-b border-zinc-700 w-full p-4 hover:bg-zinc-800" href="{$this->href}">{$this->text}</a>
        HTML;
    }
}