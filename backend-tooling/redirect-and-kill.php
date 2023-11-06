<?php

function redirect_and_kill(string $url): void {
    header("Location: " . $url);
    die();
}