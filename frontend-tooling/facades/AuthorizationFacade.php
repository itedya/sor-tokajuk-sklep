<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "AuthorizationFacade.php")) {
    http_response_code(404);
    die();
}

class AuthorizationFacade
{
    public static function isAuthorized()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    public static function authorize($id)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user_id'] = $id;
    }

    public static function unauthorize()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['user_id']);
    }

    public static function getUserId()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!self::isAuthorized()) {
            return null;
        }

        return $_SESSION['user_id'];
    }

    public static function redirectIfUnauthorized()
    {
        if (!self::isAuthorized()) {
            header('Location: /auth/login.php');
            die();
        }
    }

    public static function redirectIfAuthorized() {
        if (self::isAuthorized()) {
            header('Location: /index.php');
            die();
        }
    }
}