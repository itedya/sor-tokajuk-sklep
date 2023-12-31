<?php

foreach (file(__DIR__ . '/../../.env') as $line) {
    $items = explode('=', $line);
    $key = trim($items[0]);
    $value = trim(join("=", array_slice($items, 1)));

    $_ENV[$key] = $value;
}

function config(string $path)
{
    $pathParts = explode(".", $path);
    $config = require __DIR__ . "/{$pathParts[0]}.php";

    return $config[$pathParts[1]];
}