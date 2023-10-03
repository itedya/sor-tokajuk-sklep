<?php

checkIfLoadedStraightfordwardly(__FILE__);

return [
    'host' => $_ENV['DATABASE_HOST'],
    'username' => $_ENV['DATABASE_USERNAME'],
    'password' => $_ENV['DATABASE_PASSWORD'],
    'database' => $_ENV['DATABASE_NAME']
];