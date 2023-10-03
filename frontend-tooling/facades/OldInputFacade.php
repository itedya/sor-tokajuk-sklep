<?php

checkIfLoadedStraightfordwardly(__FILE__);

class OldInputFacade
{
    private static function start_session()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function add(string $index, ?string $data)
    {
        if ($data == null) {
            return;
        }

        self::start_session();

        if (!isset($_SESSION['__old_input'])) {
            $_SESSION['__old_input'] = [];
        }

        $_SESSION['__old_input'][$index] = $data;
    }

    public
    static function clear()
    {
        self::start_session();

        unset($_SESSION['__old_input']);
    }

    public
    static function has($index)
    {
        self::start_session();

        return self::get($index) !== null;
    }

    public
    static function get($index)
    {
        self::start_session();

        if (!isset($_SESSION['__old_input'])) {
            return null;
        }

        if (!isset($_SESSION['__old_input'][$index])) {
            return null;
        }

        return $_SESSION['__old_input'][$index];
    }

    public
    static function hasErrors(): bool
    {
        self::start_session();

        return isset($_SESSION['__old_input']);
    }

    public
    static function renderInComponent($index): string
    {
        if (ValidationErrorFacade::has($index)) {
            return (new ErrorMessage(ValidationErrorFacade::get($index)))->render();
        } else {
            return "";
        }
    }
}