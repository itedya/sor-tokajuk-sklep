<?php
require_once "./frontend-tooling/autoload.php";
loadFrontendTooling(".");

session_start();

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "nie zalogowany";

$body = <<<HTML
<div class="flex justify-center items-center p-4">
    <h1 class="text-3xl text-zinc-300">Strona główna $userId</h1>
</div>
HTML;

echo (new Layout($body))->render();
?>
