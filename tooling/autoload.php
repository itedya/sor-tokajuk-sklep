<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/helper_functions/abort.php";
require_once __DIR__ . "/helper_functions/auth.php";
require_once __DIR__ . "/helper_functions/database.php";
require_once __DIR__ . "/helper_functions/gates.php";
require_once __DIR__ . "/helper_functions/get_query_params.php";
require_once __DIR__ . "/helper_functions/mail.php";
require_once __DIR__ . "/helper_functions/old_input.php";
require_once __DIR__ . "/helper_functions/redirect_and_kill.php";
require_once __DIR__ . "/helper_functions/session.php";
require_once __DIR__ . "/helper_functions/validation_errors.php";

require_once __DIR__ . "/config/index.php";

require_once __DIR__ . "/PHPMailer-master/src/Exception.php";
require_once __DIR__ . "/PHPMailer-master/src/SMTP.php";
require_once __DIR__ . "/PHPMailer-master/src/PHPMailer.php";

require_once __DIR__ . "/components/render_in_layout.php";
require_once __DIR__ . "/components/render_navbar.php";
require_once __DIR__ . "/components/render_textfield.php";

session_initialize();