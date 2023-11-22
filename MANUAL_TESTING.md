# Testy manualne

## Logowanie

`auth/login.php`

- [x] Strona wyświetla "Dane do logowania nie zgadzają się.", gdy podano błędne dane logowania
- [x] Strona wyświetla "Konto nie zostało jeszcze zweryfikowane.", gdy podano poprawne dane logowania, ale email nie
  został potwierdzony
- [x] Strona przekierowuje do strony głównej po zalogowaniu, gdy podano poprawne dane logowania
- [x] Strona waliduje podany email
- [x] Strona waliduje podane hasło
- [x] Strona sprawdza poprawność formatu email (min 6 znaków, max 320 znaków, filter_var email)
- [x] Strona sprawdza poprawność długości hasła (min. 8 znaków, max 64 znaki, minimum 1 mala litera, minimum 1 duża
  litera, minimum 1 cyfra, minimum 1 znak specjalny, bez spacji, bez polskich znaków i jakichkolwiek diakrytycznych)
- [x] Strona loguje użytkownika, gdy podano poprawne dane logowania i przekierowuje do strony głównej
- [x] Strona odrzuca request, jak jest się zalogowanym (POST i GET)
- [x] Kliknięcie "Nie pamiętam hasła" przekierowuje do /auth/forgot-password.php

## Rejestracja

`auth/register.php`

- [x] Strona daje możliwość rejestracji, gdy podano poprawne dane
- [x] Strona wyświetla "Podane hasła nie są takie same", gdy podano różne hasła
- [x] Strona wyświetla "Podany email jest już zajęty", gdy podano zajęty email
- [x] Strona sprawdza poprawność formatu email (min 6 znaków, max 320 znaków, filter_var email)
- [x] Strona sprawdza poprawność długości hasła (min. 8 znaków, max 64 znaki, minimum 1 mala litera, minimum 1 duża
  litera, minimum 1 cyfra, minimum 1 znak specjalny, bez spacji, bez polskich znaków i jakichkolwiek diakrytycznych)
- [x] Strona dodaje użytkownika do bazy danych, gdy podano poprawne dane
- [x] Strona prosi o potwierdzenie emaila po rejestracji
- [x] Strona wysyła maila z potwierdzeniem maila po rejestracji
- [x] Strona odrzuca request, jak jest się zalogowanym (POST i GET)

## Potwierdzanie maila

`auth/confirm-email.php`

- [x] Strona akceptuje request, gdy podano poprawny hash potwierdzający
- [x] Strona wyświetla "Twój email został zweryfikowany", gdy podano poprawny hash potwierdzający, a email nie jest
  potwierdzony
- [x] Strona odznacza, że użytkownik jest zweryfikowany, jeżeli hash jest poprawny
- [x] Strona odrzuca request, gdy podano błędny hash potwierdzający
- [x] Strona przekierowuje do strony głównej, gdy podano poprawny hash potwierdzający, a email jest już potwierdzony
- [x] Strona odrzuca request, jak jest się zalogowanym

## Resetowanie hasła

`auth/forgot-password.php`

- [x] Strona wyświetla "Użytkownik o takim emailu nie istnieje.", gdy podano nieistniejący email
- [x] Strona sprawdza poprawność formatu email (min 6 znaków, max 320 znaków, filter_var email)
- [x] Strona dodaje wpis do bazy danych, gdy podano poprawny email
- [x] Strona wysyła maila z linkiem do resetowania hasła, gdy podano poprawny email
- [x] Strona wyświetla "Na ten adres email została już wysłana prośba o zmianę hasła.", gdy podano poprawny email, a
  email został już wysłany (max godzina ważności poprzedniego resetu)
- [x] Strona przekierowuje do `auth/after-forgot-password.php` gdy podano poprawny email.
- [x] Strona przekierowuje do ekranu logowania, kiedy kliknięto "Wróć do logowania"
- [x] Strona zastępuje przycisk napisem "Proszę czekać" i go wyłącza, jak wyśle się formularz

## Resetowanie hasła - po wysłaniu pierwszego formularza

`auth/after-forgot-password.php`

- [x] Strona wyświetla "Teraz sprawdź swojego maila" oraz "Na twój adres email został wysłany link z resetem hasła."
- [x] Strona nie wpuszcza ludzi zalogowanych
- [x] Strona nie wpuszcza ludzi nie będących od razu po wysyłce pierwszego formularza

## Resetowanie hasła - potwierdzenie

`auth/new-password.php`

- [x] Strona odrzuca request, gdy podano niepoprawny hash, jest on pusty lub go nie ma
- [x] Strona akceptuje request, gdy podano poprawny hash
- [x] Strona odrzuca request, gdy podano poprawny hash, ale jest on przedawniony
- [x] Strona renderuje formularz, gdy podano poprawny hash
- [x] Strona wyświetla "Hasła się nie zgadzają.", gdy podano różne hasła
- [x] Strona sprawdza poprawność długości hasła (min. 8 znaków, max 64 znaki, minimum 1 mala litera, minimum 1 duża
  litera, minimum 1 cyfra, minimum 1 znak specjalny, bez spacji, bez polskich znaków i jakichkolwiek diakrytycznych)
- [x] Strona wyświetla "Twoje hasło zostało zresetowane, możesz już się zalogować.", gdy podano poprawne hasła
- [x] Strona zmienia hasło w bazie danych, gdy podano poprawne hasła
- [x] Strona usuwa wpis z bazy danych o resecie, gdy podano poprawne hasła

## Panel klienta

`client-panel/index.php`

- [ ] Strona odrzuca request, gdy nie jest się zalogowanym
- [ ] Strona wyświetla email zalogowanego użytkownika
- [ ] Strona wyświetla zamówienia zalogowanego użytkownika
- [ ] Strona wyświetla listę ulubionych produktów zalogowanego użytkownika