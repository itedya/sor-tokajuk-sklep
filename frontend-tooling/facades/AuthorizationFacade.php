<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "AuthorizationFacade.php")) {
    http_response_code(404);
    die();
}

class AuthorizationFacade
{
    public static function isAuthorized()
    {
        return isset($_SESSION['user_id']);
    }

    public static function authorize($id)
    {
        session_start();

        $_SESSION['user_id'] = $id;
    }

    public static function unauthorize()
    {
        session_start();

        unset($_SESSION['user_id']);
    }

    public static function getUserId()
    {
        session_start();

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