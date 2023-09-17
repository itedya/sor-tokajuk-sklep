<?php

function dd($data) {
    header('Content-Type: text/plain');
    die(var_dump($data));
}