<?php

use components\Layout;
use facades\AuthorizationFacade;

require_once "./frontend-tooling/autoload.php";
loadFrontendTooling(".");

$userId = AuthorizationFacade::getUserId() ?? "niezalogowany";

function afterRegistrationMessage(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    if (!isset($_SESSION['after_registration'])) return "";

    unset($_SESSION['after_registration']);

    return <<<HTML
        <div class="text-xl bg-green-800 text-zinc-300 p-4 rounded-xl">
            Zarejestrowano pomyślnie, musisz teraz potwierdzić maila.
        </div>
    HTML;
}

$message = afterRegistrationMessage();

$body = <<<HTML
<div class="flex flex-col justify-center items-center p-4 gap-4">
{$message}
    <h1 class="text-3xl text-zinc-300">Strona główna $userId</h1>
</div>
HTML;

echo (new Layout($body))->render();
?>
