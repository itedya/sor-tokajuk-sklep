<?php
function auth_is_logged_in(): bool
{
    return session_has('logged_in_user_id');
}

function auth_login(int $userId): void
{
    session_set('logged_in_user_id', $userId);
}

function auth_logout(): void
{
    session_remove('logged_in_user_id');
}

function auth_get_user_id(): ?int
{
    return session_get('logged_in_user_id');
}

function auth_is_admin(): bool
{
    if (!auth_is_logged_in()) return false;

    $userId = auth_get_user_id();

    db_transaction(function (mysqli $db) use ($userId, &$isAdmin) {
        $user = db_query_row($db, "SELECT * FROM users WHERE id = ?", [$userId]);

        $isAdmin = $user['is_admin'];
    });

    return $isAdmin;
}