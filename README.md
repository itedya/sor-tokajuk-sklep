# Projekt sklepu internetowego

**Wersja PHP**: 8.1.x  
**Wersja MySQL**: 8.x  
**Proponowana wersja XAMPP:** 8.1.17

## Logowanie

**Metoda**: POST
**URL**: /auth/login.php

```json
{
  "email": "string",
  "password": "string"
}
```

jeżeli hasło złe lub email nie odpowiada żadnemu użytkownikowi w bazie to wróć do /auth/login.php i w sesji daj errory
jeżeli ok to przekieruj do /index.php i utwórz sesję z użytkownikiem

## Rejestracja

**Metoda**: POST
**URL**: /auth/register.php

```json
{
  "email": "string",
  "password": "string",
  "confirm_password": "string"
}
```

jeżeli hasło za krótkie, albo email za krótki itd. czyli walidcja to wróć do /auth/register.php i w sesji daj errory
jeżeli email zajęty to wróć do /auth/register.php i w sesji daj errory
jeżeli ok to w sesji daj powiadomienie, że pomyślnie zarejestrowano i przekieruj do /auth/login.php