<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$userId = auth_get_user_id();

$db = get_db_connection();
$user = database_users_get_by_id($db, $userId);

if ($user === null) {
    throw new Exception("User not found");
}

echo render_in_layout(function () use ($user) { ?>
    <div class="container mx-auto flex flex-row gap-8 p-4">
        <div class="flex flex-col gap-4 w-full text-neutral-200">
            <div class="flex flex-col gap-2 text-center">
                <h1 class="text-3xl font-bold">Panel klienta</h1>
                <p class="text-xl">Witaj, <?= htmlspecialchars($user['email']) ?>!</p>
            </div>

            <a href="<?= base_url("/management/users/edit.php", ['id' => $user['id']]) ?>"
               class="px-4 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg text-center">Edytuj swoje dane</a>
        </div>
    </div>
<?php });