<?php

Router::addRoute(new Route("/api/status", "GET", function () {
    echo "API test route";
}));