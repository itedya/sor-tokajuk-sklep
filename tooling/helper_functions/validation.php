<?php

function validate_email(string $email): ?string
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== $email) {
        return "Email jest niepoprawny";
    }

    if (strlen($email) < 6) {
        return "Email jest za krótki";
    }

    if (strlen($email) > 320) {
        return "Email jest za długi";
    }

    return null;
}

function validate_password(string $password): ?string
{
    if (strlen($password) < 8) return "Hasło musi mieć co najmniej 8 znaków";
    if (strlen($password) > 64) return "Hasło może mieć maksymalnie 64 znaki";

    if (preg_match("/^([a-z]|[A-Z]|[0-9]|[_\\-~`!@#$%^&*()+=\\[\\];:'\"\\/?.>,<])+$/", $password) !== 1) {
        return "Hasło może zawierać tylko małe i duże litery, cyfry oraz znaki specjalne. Bez spacji i polskich znaków.";
    }

    if (preg_match("/[a-z]/", $password) !== 1) {
        return "Hasło musi zawierać co najmniej jedną małą literę bez polskich znaków.";
    }

    if (preg_match("/[A-Z]+/", $password) !== 1) {
        return "Hasło musi zawierać co najmniej jedną wielką literę bez polskich znaków.";
    }

    if (preg_match("/[0-9]+/", $password) !== 1) {
        return "Hasło musi zawierać co najmniej jedną cyfrę.";
    }

    if (preg_match("/[_\\-~`!@#$%^&*()+=\\[\\];:'\"\\/?.>,<]+/", $password) !== 1) {
        return "Hasło musi zawierać co najmniej jeden znak specjalny.";
    }

    return null;
}