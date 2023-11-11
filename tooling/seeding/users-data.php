<?php

return [
    [
        'id' => 1,
        'username' => 'localadmin@localhost.local',
        'password' => password_hash('localadmiN123!', PASSWORD_BCRYPT),
        'is_verified' => true,
        'is_admin' => true,
        'created_at' => date('Y-m-d H:i:s')
    ]
];