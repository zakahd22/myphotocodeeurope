<?php
require_once  "../common/global.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
exec("ps aux | grep copiaEvent.php", $exec);
foreach ($exec as $ex) {
    $txt = substr($ex, -30);
    var_dump($txt);
    if($txt == "/usr/bin/php7.1 copiaEvent.php"){        
        utils::log("en execucio", "logInitCopia");
	exit;
    }
}
utils::log("comença script", "logInitCopia");
//exec("/usr/bin/php7.1 copiaEvent.php &");
pclose(popen("/usr/bin/php7.1 copiaEvent.php", 'r'));
