<?php

function get_query_params(): array
{
    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if ($query === null || $query === false) {
        return [];
    }

    parse_str($query, $parsedQueryParameters);

    return $parsedQueryParameters;
}

function get_query_param(string $key): ?string
{
    $params = get_query_params();

    return $params[$key] ?? null;
}