<?php

class Router
{
    private static $routes = [];

    public static function addRoute($route)
    {
        Router::$routes[] = $route;
    }

    public static function match($uri, $method)
    {
        foreach (Router::$routes as $route) {
            if ($route->matches($uri, $method)) {
                return $route;
            }
        }

        return null;
    }
}