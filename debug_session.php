<?php
require_once __DIR__ . '/common/global.php';
header('Content-Type: text/plain; charset=utf-8');
echo "session_save_path: " . session_save_path() . "\n";
echo "session_id (PHP): " . session_id() . "\n";
echo "_COOKIE PHPSESSID: " . (isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '(none)') . "\n\n";
echo "\
_SESSION:\n";
print_r($_SESSION);
