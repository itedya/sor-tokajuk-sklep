<?php

function config(string $path)
{
    $pathParts = explode(".", $path);
    $config = require_once join(DIRECTORY_SEPARATOR, [__DIR__, $pathParts[0] . ".php"]);

    return $config[$pathParts[1]];
}