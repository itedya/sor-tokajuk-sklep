<?php

use facades\AuthorizationFacade;

require_once "./../frontend-tooling/autoload.php";
loadFrontendTooling();

AuthorizationFacade::redirectIfUnauthorized();

AuthorizationFacade::unauthorize();
header("Location: ../index.php");