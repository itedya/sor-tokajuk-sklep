<?php

require_once __DIR__ . '/../tooling/autoload.php';

$db = get_db_connection();

db_drop($db);
db_migrate($db);
db_seed($db);

header("Content-Type: text/plain");
echo "Database has been successfully remigrated and seeded.";