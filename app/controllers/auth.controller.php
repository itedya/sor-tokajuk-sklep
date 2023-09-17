<?php

class AuthController
{
    private static $instance = null;

    /**
     * @return null
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            return self::$instance = new AuthController();
        }

        return self::$instance;
    }

    public function __construct()
    {
        // ...
    }

    public function register()
    {
        view("register", []);
    }

    public function login()
    {
        view("login", []);
    }
}