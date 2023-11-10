# Testy manualne

## Logowanie

`auth/login.php`

- [ ] Strona wyświetla "Dane nie zgadzają się", gdy podano błędne dane logowania
- [ ] Strona wyświetla "Email nie został potwierdzony", gdy podano poprawne dane logowania, ale email nie został
  potwierdzony
- [ ] Strona przekierowuje do strony głównej po zalogowaniu, gdy podano poprawne dane logowania
- [ ] Strona waliduje podany email
- [ ] Strona waliduje podane hasło
- [ ] Strona sprawdza poprawność formatu email (max 320 znaków, filter_var email)
- [ ] Strona sprawdza poprawność długości hasła (min. 8 znaków, max 64)
- [ ] Strona loguje użytkownika, gdy podano poprawne dane logowania

## Rejestracja

`auth/register.php`

- [ ] Strona daje możliwość rejestracji, gdy podano poprawne dane
- [ ] Strona wyświetla "Podane hasła nie są takie same", gdy podano różne hasła
- [ ] Strona wyświetla "Podany email jest już zajęty", gdy podano zajęty email
- [ ] Strona dodaje użytkownika do bazy danych, gdy podano poprawne dane
- [ ] Strona sprawdza poprawność formatu email (max 320 znaków, filter_var email)
- [ ] Strona sprawdza poprawność długości hasła (min. 8 znaków, max 64)
- [ ] Strona prosi o potwierdzenie emaila po rejestracji
- [ ] Strona wysyła maila z potwierdzeniem maila po rejestracji

## Potwierdzanie maila

`auth/confirm-email.php`

- [ ] Strona odrzuca request, gdy podano błędny hash potwierdzający
- [ ] Strona akceptuje request, gdy podano poprawny hash potwierdzający
- [ ] Strona wyświetla "Email został już potwierdzony", gdy podano poprawny hash potwierdzający, a email jest już
  potwierdzony
- [ ] Strona wyświetla "Twój email został zweryfikowany", gdy podano poprawny hash potwierdzający, a email nie jest
  potwierdzony

## Resetowanie hasła

`auth/forgot-password.php`

- [ ] Strona wyświetla "Użytkownik o takim emailu nie istnieje.", gdy podano nieistniejący email
- [ ] Strona waliduje podany email
- [ ] Strona dodaje wpis do bazy danych, gdy podano poprawny email
- [ ] Strona wysyła maila z linkiem do resetowania hasła, gdy podano poprawny email
- [ ] Strona wyświetla "Email został już wysłany.", gdy podano poprawny email, a email został już wysłany
- [ ] Strona wyświetla "Email został wysłany.", gdy podano poprawny email.

## Resetowanie hasła - potwierdzenie

`auth/new-password.php`

- [ ] Strona odrzuca request, gdy podano niepoprawny hash
- [ ] Strona akceptuje request, gdy podano poprawny hash
- [ ] Strona odrzuca request, gdy podano poprawny hash, ale jest on przedawniony
- [ ] Strona renderuje formularz, gdy podano poprawny hash
- [ ] Strona wyświetla "Hasła nie są takie same.", gdy podano różne hasła
- [ ] Strona waliduje podane hasła (min. 8 znaków, max 64)
- [ ] Strona wyświetla "Hasło zostało zmienione.", gdy podano poprawne hasła
- [ ] Strona zmienia hasło w bazie danych, gdy podano poprawne hasła
- [ ] Strona usuwa wpis z bazy danych o resecie, gdy podano poprawne hasła
