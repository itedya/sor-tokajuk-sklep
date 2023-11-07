<?php

function auth_get_logged_in_user_id(): ?int {
    return AuthorizationFacade::getUserId();
}

function auth_redirect_if_not_logged_in() {
    if (is_null(auth_get_logged_in_user_id())) {
        header("Location: " . config("app.url") . "/auth/login.php");
    }
}

function auth_redirect_if_logged_in() {
    if (!is_null(auth_get_logged_in_user_id())) {
        header("Location: " . config("app.url") . "/");
    }
}