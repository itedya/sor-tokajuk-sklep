<?php

class Layout implements Component
{
    private $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function render()
    {
        $navbar = (new Navbar())->render();

        return <<<HTML
            <!doctype html>
            <html lang="pl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>Rejestracja</title>
                <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="bg-zinc-900 min-h-screen w-full">
            $navbar
                
            <main class="pt-24 min-h-screen">
                $this->body
            </main>
            </body>
            </html>
        HTML;
    }
}