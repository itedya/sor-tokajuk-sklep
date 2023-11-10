<?php

require_once './tooling/autoload.php';

$userId = auth_get_user_id();

function isAfterRegistration(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    if (!isset($_SESSION['after_registration'])) return false;

    unset($_SESSION['after_registration']);

    return true;
}

if (isAfterRegistration()) {
    echo render_in_layout(function () { ?>
        <div class="text-xl bg-green-800 text-zinc-300 p-4 rounded-xl">
            Zarejestrowano pomyślnie, musisz teraz potwierdzić maila.
        </div>
    <?php });
} else {
    echo render_in_layout(function () use ($userId) { ?>
        Strona główna <?= $userId ?>
    <?php });
}

?>
