<?php

require_once '../tooling/autoload.php';

AuthorizationFacade::redirectIfUnauthorized();

AuthorizationFacade::unauthorize();
header("Location: ../index.php");