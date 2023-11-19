<?php

function base_url(string $path, array $params = []): string
{
    $url = config('app.url');
    if (str_ends_with($url, "/")) $url = substr($url, 0, strlen($url) - 1);
    if (str_starts_with($path, "/")) $path = substr($path, 1);


    return join("/", [$url, $path]) . "?" . http_build_query($params);
}