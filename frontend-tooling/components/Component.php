<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "Component.php")) {
    http_response_code(404);
    die();
}

interface Component
{
    public function render();
}