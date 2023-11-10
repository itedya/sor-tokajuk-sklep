<?php

function validation_errors_add(string $index, string $message): void
{
    $data = session_get('__validation_errors', []);

    $data[$index] = $message;

    session_flash('__validation_errors', $data);
}

function validation_errors_has(string $index): bool
{
    $data = session_get('__validation_errors');

    return isset($data[$index]);
}

function validation_errors_get($index): ?string
{
    $data = session_get('__validation_errors');

    return $data[$index] ?? null;
}

function validation_errors_is_empty(): bool
{
    $data = session_get('__validation_errors', []);

    return count($data) === 0;
}
