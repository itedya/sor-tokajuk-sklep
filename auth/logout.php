<?php

require_once '../tooling/autoload.php';

gate_redirect_if_unauthorized();

redirect_and_kill("../index.php");
