<?php

if (str_ends_with($_ENV['APP_URL'], '/')) {
    $_ENV['APP_URL'] = substr($_ENV['APP_URL'], 0, -1);
}

return [
    'url' => $_ENV['APP_URL']
];