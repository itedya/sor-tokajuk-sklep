<?php

Router::addRoute(new Route("/auth/register", "GET", function () {
    echo "Register";
}));

Router::addRoute(new Route("/auth/login", "GET", function () {
    echo "Login";
}));

Router::addRoute(new Route("/", "GET", function () {
    echo "Home";
}));