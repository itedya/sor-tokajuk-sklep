<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/database.php";

require_once __DIR__ . "/abort.php";
require_once __DIR__ . "/session.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/get-query-params.php";
require_once __DIR__ . "/redirect-and-kill.php";
require_once __DIR__ . "/PHPMailer-master/src/Exception.php";
require_once __DIR__ . "/PHPMailer-master/src/SMTP.php";
require_once __DIR__ . "/PHPMailer-master/src/PHPMailer.php";
require_once __DIR__ . "/mail.php";
