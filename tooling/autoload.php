<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/helper_functions/database.php";
require_once __DIR__ . "/helper_functions/abort.php";
require_once __DIR__ . "/helper_functions/session.php";
require_once __DIR__ . "/helper_functions/auth.php";
require_once __DIR__ . "/helper_functions/get-query-params.php";
require_once __DIR__ . "/helper_functions/redirect-and-kill.php";
require_once __DIR__ . "/helper_functions/mail.php";

require_once __DIR__ . "/config/index.php";

require_once __DIR__ . "/facades/AuthorizationFacade.php";
require_once __DIR__ . "/facades/OldInputFacade.php";
require_once __DIR__ . "/facades/ValidationErrorFacade.php";

require_once __DIR__ . "/PHPMailer-master/src/Exception.php";
require_once __DIR__ . "/PHPMailer-master/src/SMTP.php";
require_once __DIR__ . "/PHPMailer-master/src/PHPMailer.php";

require_once __DIR__ . "/components/Component.php";
require_once __DIR__ . "/components/ErrorMessage.php";
require_once __DIR__ . "/components/Layout.php";
require_once __DIR__ . "/components/MobileNavbarItem.php";
require_once __DIR__ . "/components/Navbar.php";
require_once __DIR__ . "/components/NavbarItem.php";
