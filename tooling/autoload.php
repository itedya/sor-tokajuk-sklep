<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/helper_functions/abort.php";
require_once __DIR__ . "/helper_functions/auth.php";
require_once __DIR__ . "/helper_functions/database.php";
require_once __DIR__ . "/helper_functions/database_users.php";
require_once __DIR__ . "/helper_functions/database_orders.php";
require_once __DIR__ . "/helper_functions/database_delivery_method.php";
require_once __DIR__ . "/helper_functions/database_products.php";
require_once __DIR__ . "/helper_functions/database_payment_types.php";
require_once __DIR__ . "/helper_functions/database_favourite_products.php";
require_once __DIR__ . "/helper_functions/database_addresses.php";
require_once __DIR__ . "/helper_functions/gates.php";
require_once __DIR__ . "/helper_functions/get_query_params.php";
require_once __DIR__ . "/helper_functions/mail.php";
require_once __DIR__ . "/helper_functions/old_input.php";
require_once __DIR__ . "/helper_functions/redirect_and_kill.php";
require_once __DIR__ . "/helper_functions/session.php";
require_once __DIR__ . "/helper_functions/validation.php";
require_once __DIR__ . "/helper_functions/validation_errors.php";
require_once __DIR__ . "/helper_functions/clear_unused.php";
require_once __DIR__ . "/helper_functions/base_url.php";
require_once __DIR__ . "/helper_functions/slugify.php";

require_once __DIR__ . "/config/index.php";

require_once __DIR__ . "/PHPMailer-master/src/Exception.php";
require_once __DIR__ . "/PHPMailer-master/src/SMTP.php";
require_once __DIR__ . "/PHPMailer-master/src/PHPMailer.php";

require_once __DIR__ . '/components/render_column_table.php';
require_once __DIR__ . '/components/render_table.php';
require_once __DIR__ . "/components/render_in_layout.php";
require_once __DIR__ . "/components/render_navbar.php";
require_once __DIR__ . "/components/render_textfield.php";
require_once __DIR__ . "/components/render_select.php";

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

set_exception_handler(function (Throwable $e) {
    while (count(ob_list_handlers()) > 0) {
        ob_end_clean();
    }

    http_response_code(500);
    header("Content-Type: text/plain");
    echo "Message: " . $e->getMessage() . PHP_EOL;
    echo "Code: " . $e->getCode() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
});

session_initialize();
