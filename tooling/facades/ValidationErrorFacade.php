<?php

class ValidationErrorFacade
{
    private static function start_session()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function add(string $index, string $message)
    {
        self::start_session();

        if (!isset($_SESSION['__validation_errors'])) {
            $_SESSION['__validation_errors'] = [];
        }

        $_SESSION['__validation_errors'][$index] = $message;
    }

    public static function clear()
    {
        self::start_session();

        unset($_SESSION['__validation_errors']);
    }

    public static function has($index)
    {
        self::start_session();

        return self::get($index) !== null;
    }

    public static function get($index)
    {
        self::start_session();

        if (!isset($_SESSION['__validation_errors'])) {
            return null;
        }

        if (!isset($_SESSION['__validation_errors'][$index])) {
            return null;
        }

        return $_SESSION['__validation_errors'][$index];
    }

    public static function hasErrors(): bool
    {
        self::start_session();

        return isset($_SESSION['__validation_errors']);
    }

    public static function renderInComponent($index): string
    {
        if (ValidationErrorFacade::has($index)) {
            return (new ErrorMessage(ValidationErrorFacade::get($index)))->render();
        } else {
            return "";
        }
    }
}