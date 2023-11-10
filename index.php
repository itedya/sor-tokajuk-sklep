<?php

require_once './tooling/autoload.php';

$userId = auth_get_user_id();

echo render_in_layout(function () use ($userId) { ?>
    <div class="text-3xl text-center text-neutral-300 p-4">
        Strona główna <?= $userId ?>
    </div>
<?php });
