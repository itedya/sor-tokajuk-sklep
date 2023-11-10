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