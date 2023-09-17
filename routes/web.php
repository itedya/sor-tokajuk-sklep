<?php

Router::addRoute(new Route("/auth/register", "GET", function () {
    AuthController::getInstance()->register();
}));

Router::addRoute(new Route("/auth/login", "GET", function () {
    AuthController::getInstance()->login();
}));

Router::addRoute(new Route("/", "GET", function () {
    echo "Home";
}));