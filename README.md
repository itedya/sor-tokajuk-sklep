# Projekt sklepu internetowego

**Wersja PHP**: 8.1.x  
**Wersja MySQL**: 8.x  
**Proponowana wersja XAMPP:** 8.1.17

## Logowanie

**Metoda**: POST
**URL**: /api/auth/login.php

```json
{
  "email": "string",
  "password": "string"
}
```

jeżeli hasło złe lub email nie odpowiada żadnemu użytkownikowi w bazie to status 400 + jakaś odpowiedź
jeżeli ok kod 204 i fajrant

## Rejestracja

**Metoda**: POST
**URL**: /api/auth/register.php

```json
{
  "email": "string",
  "password": "string",
  "confirm_password": "string"
}
```

jeżeli hasło za krótkie, albo email za krótki itd. czyli walidcja to też jakaś odpowiedź + status 400  
jeżeli email zajęty to kod 409 i jakaś odpowiedź  
jeżeli ok kod 204 i fajrant