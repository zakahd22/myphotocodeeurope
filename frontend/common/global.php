<?php
//function exceptions_error_handler($severity, $message, $filename, $lineno) {
//    throw new ErrorException($message, 0, $severity, $filename, $lineno);
//}
//
//set_error_handler('exceptions_error_handler');


//echo "<p>__DIR__".__DIR__."</p>";
//echo "<p>dirname (__FILE__)".dirname (__FILE__)."</p>";

//error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
//error_reporting(E_ALL ^ E_WARNING); // Error/Exception engine, E_ALL except Warnings
error_reporting(E_ALL); 
ini_set('ignore_repeated_errors', TRUE); 
ini_set('display_errors', TRUE); 
ini_set('log_errors', FALSE); 
//ini_set('error_log', __DIR__ . "/../../logsMyPC/log_reporting_common_global-".date("Ymd").".dat");
//error_log( "TO_DELETE checking path" );
//202412logsMyPC 
//ob_start();
function is_session_started(){
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } 
        else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

if(is_session_started() === FALSE) session_start();

//a cada session start incloure global, si s'accedeix a la bd cridar el conexio.php
//replace(common, "") IMPORTANT que no existeixi cap més directori common al cami cap al fitxer!!
//define("G_PATH", dirname(__FILE__)."/../");

//G_PATH getting the position + 1 of the last slash 
$path = dirname (__FILE__);
$G_PATH = dirname($path) . DIRECTORY_SEPARATOR;

define("G_PATH", $G_PATH);

//require_once G_PATH . "common/config/config.php";
require_once G_PATH . "common/config/params.php";

require_once G_PATH . "common/utils.php";

require_once G_PATH . "common/mail.php";

require_once G_PATH . "common/Classes/EntityController.php";

require_once G_PATH . '/vendor/autoload.php';

try {
    // G_PATH ends in …/httpdocs
    $appRoot = dirname(rtrim(G_PATH, '/'));  // …/myphotocode.com
    $envDir  = $appRoot . '/env';            // …/myphotocode.com/env
    $envFile = $envDir . '/.env';

    if (class_exists('\Dotenv\Dotenv') && file_exists($envFile)) {
        \Dotenv\Dotenv::createImmutable($envDir)->load();
    } else {
        error_log("Dotenv not loaded; exists? "
            . (file_exists($envFile) ? 'Yes' : 'No')
        );
    }
} catch (\Exception $e) {
    error_log('Error loading .env: ' . $e->getMessage());
}


//require_once G_PATH . "common/dictionary.php";
//require_once G_PATH . "common/mail.php";

if (isset($_GET['lang'])) {
    $LANG = $_GET['lang'];
}
elseif(isset($_SESSION['LANG'])) {
    $LANG = $_SESSION['LANG'];
}
else{
    $LANG = 'en-US';
}


$_SESSION['LANG'] = $LANG;
$_SESSION['RENT_NAME'] = G_RENTNAME;
define("G_ASSETS", 'assets/');
$_SESSION['ASSETS'] = G_ASSETS;
define("G_HTML", 'html/');
$_SESSION['HTML'] = G_HTML;

define("G_ASSETS_PATH", G_PATH . G_ASSETS);
$_SESSION['ASSETS_PATH'] = G_ASSETS_PATH;
$_SESSION["IMG_PATH"] = $_SESSION['ASSETS']."img/" . $_SESSION['LANG'] . "/";
//ob_clean();
//202412logsMyPC

//error_log( "TO_DELETE global end" );