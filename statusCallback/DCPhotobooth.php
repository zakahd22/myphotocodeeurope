<?php
error_reporting(E_ERROR | E_PARSE); // Fatal & parse Error
ini_set('ignore_repeated_errors', TRUE); // always use TRUE
ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', "../logsMyPC/log_callBack-DCPhotobooth-".date("Ymd").".dat"); // Logging file path

error_log(var_export($_REQUEST, true));

