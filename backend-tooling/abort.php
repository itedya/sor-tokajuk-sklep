<?php

function abort(int $statusCode): void {
    http_response_code($statusCode);
    die();
}