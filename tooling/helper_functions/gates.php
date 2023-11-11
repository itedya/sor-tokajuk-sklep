<?php

function gate_redirect_if_unauthorized(): void
{
    if (!auth_is_logged_in()) {
        redirect_and_kill(config("app.url") . "/auth/login.php");
    }
}

function gate_redirect_if_logged_in(): void
{
    if (auth_is_logged_in()) {
        redirect_and_kill(config("app.url") . "/");
    }
}

function gate_redirect_if_not_an_admin(): void
{
    if (!auth_is_admin()) {
        redirect_and_kill(config("app.url") . "/");
    }
}