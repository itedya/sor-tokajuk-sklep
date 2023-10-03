<?php

checkIfLoadedStraightfordwardly(__FILE__);

function config(string $path)
{
    $pathParts = explode(".", $path);
    $config = require join(DIRECTORY_SEPARATOR, [__DIR__, $pathParts[0] . ".php"]);

    return $config[$pathParts[1]];
}