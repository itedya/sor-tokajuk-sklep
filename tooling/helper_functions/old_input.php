<?php

function old_input_add(string $key, mixed $data): void
{
    $data = session_get('__old_input', []);

    $data[$key] = $data;

    session_flash('__old_input', $data);
}

function old_input_has(string $index): bool
{
    $data = session_get('__old_input');
    return isset($data[$index]);
}

function old_input_get(string $index): mixed
{
    $data = session_get('__old_input');
    return $data[$index] ?? null;
}

function old_input_get_safe(string $index): string
{
    if (!old_input_has($index)) return "";
    return htmlspecialchars(old_input_get($index), ENT_QUOTES);
}