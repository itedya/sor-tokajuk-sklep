<?php

require_once __DIR__ . '/../tooling/autoload.php';

gate_redirect_if_unauthorized();
gate_redirect_if_not_an_admin();

$users = db_query_rows(get_db_connection(), "SELECT * FROM users", []);

echo render_in_layout(function () use ($users) { ?>
    <div class="container mx-auto p-4 gap-8 flex flex-col">
        <div class="text-3xl text-center text-neutral-300 p-4">
            Użytkownicy
        </div>

        <div class="flex flex-row justify-end items-center gap-4">
            <a href="<?= config("app.url") . "/management/users/create.php" ?>"
               class="px-8 py-2 bg-green-600 text-neutral-200 font-semibold rounded-lg">
                Dodaj pracownika
            </a>
        </div>

        <div class="grid grid-cols-1 auto-grid-rows gap-8 border-b">
            <?php foreach ($users as $user): ?>
                <div class="grid grid-cols-1 auto-rows-auto lg:grid-cols-5 lg:auto-rows-fr gap-4 lg:justify-items-center lg:items-center border-t py-8">
                    <h2 class="lg:col-span-2 text-xl text-neutral-300 font-bold"><?= htmlspecialchars($user['email']) ?></h2>

                    <div>
                        <?php if ($user['is_admin']): ?>
                            <span class="text-neutral-200 bg-sky-600 p-2 rounded-xl font-bold">Pracownik</span>
                        <?php else: ?>
                            <span class="text-neutral-200 bg-neutral-600 p-2 rounded-xl font-bold">Klient</span>
                        <?php endif; ?>
                    </div>

                    <p class="text-neutral-200">Konto założone: <?= htmlspecialchars($user['created_at']) ?></p>

                    <div class="flex flex-row justify-end items-center gap-4">
                        <a href="<?= base_url('/management/users/edit.php', ['id' => $user['id']]) ?>"
                           class="px-4 py-2 bg-yellow-600 text-neutral-200 font-semibold rounded-lg">
                            Edytuj
                        </a>

                        <a href="<?= base_url('/management/users/delete.php', ['id' => $user['id']]) ?>"
                           class="px-4 py-2 bg-red-600 text-neutral-200 font-semibold rounded-lg">
                            Usuń
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php });
