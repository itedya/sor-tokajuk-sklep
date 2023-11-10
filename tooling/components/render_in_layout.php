<?php

function render_in_layout(callable $callback): string
{
    ob_start();
    ?>
    <!doctype html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>TrumniX - Jak umierać to tylko z nami</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-zinc-900 min-h-screen w-full">
    <main class="pt-24 min-h-screen">
        <?=render_navbar(); ?>
        <?= $callback(); ?>
    </main>
    </body>
    </html>
    <?php
    return ob_get_clean();
}