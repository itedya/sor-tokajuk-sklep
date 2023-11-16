<?php

$_SESSION_FLASH = [];

/*
 * Funkcja odpowiadająca za branie danych trwałych z sesji.
 * Jeżeli potrzebujemy jakąś wartość uzyskać, jeżeli wartość w sesji nie istnieje, to możemy ją podać jako
 * drugi argument (default value).
 */
function session_get(string $key, mixed $default = null): mixed
{
    global $_SESSION_FLASH;

    if (isset($_SESSION_FLASH[$key])) return $_SESSION_FLASH[$key];

    return $_SESSION['data'][$key] ?? $default;
}

/*
 * Funkcja odpowiadająca za ustawianie danych trwałych na sesji.
 */
function session_set(string $key, mixed $value): void
{
    $_SESSION['data'][$key] = $value;
}

function session_set_ttl(string $key, mixed $value, int $ttl_seconds): void
{
    $_SESSION['data'][$key] = $value;
    $_SESSION['ttl'][$key] = time() + $ttl_seconds;
}

function session_has(string $key): bool
{
    global $_SESSION_FLASH;

    return isset($_SESSION_FLASH[$key]) || isset($_SESSION['data'][$key]);
}

function session_remove(string $key): bool
{
    global $_SESSION_FLASH;

    if (isset($_SESSION_FLASH[$key])) {
        unset($_SESSION_FLASH[$key]);
        unset($_SESSION['flash'][$key]);
        return true;
    }

    if (isset($_SESSION['data'][$key])) {
        unset($_SESSION['data'][$key]);
        if (isset($_SESSION['ttl'][$key])) unset($_SESSION['ttl'][$key]);

        return true;
    }

    return false;
}

/*
 * Flash to pamięć chwilowa, jest ona czyszczona po każdym requeście.
 * Przykład: ustawiamy wartość 'user-id' w sesji na 1.
 *  - request 1 <- tutaj ustawiamy
 *  - request 2 <- tutaj jeszcze będzie widoczna ta wartość, na końcu tego requestu pamięć flash jest trwale kasowana.
 *  - request 3 <- tutaj już nie będzie widoczna
 */
function session_flash(string $key, mixed $value): void
{
    global $_SESSION_FLASH;

    $_SESSION_FLASH[$key] = $value;
    $_SESSION['flash'][$key] = $value;
}

/*
 * Funkcja wywoływana w autoloadzie, odpowiadająca za inicjalizacje sesji.
 */
function session_initialize(): void
{
    session_start();
    global $_SESSION_FLASH;
    $_SESSION_FLASH = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    if (isset($_SESSION['ttl'])) {
        $time = time();
        foreach (array_keys($_SESSION['ttl']) as $key) {
            if ($_SESSION['ttl'][$key] < $time) {
                unset($_SESSION['data'][$key]);
                unset($_SESSION['ttl'][$key]);
            }
        }
    }
}