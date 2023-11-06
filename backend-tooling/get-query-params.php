<?php

function get_query_params(): array {
    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if (is_null($query)) {
        return [];
    }

    $parsedQueryParameters = [];
    parse_str($query, $parsedQueryParameters);

    return $parsedQueryParameters;
}