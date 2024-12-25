<?php
error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
//error_reporting(E_ALL ^ E_WARNING); // Error/Exception engine, E_ALL except Warnings
ini_set('ignore_repeated_errors', TRUE); // always use TRUE
ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', "../logsMyPC/log_reporting_common_global-".date("Ymd").".dat"); // Logging file path
//error_log( "TO_DELETE checking path" );

//202412logsMyPC ob_start();
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
$G_PATH = substr($path, 0, (strrpos($path, '/') + 1));

define("G_PATH", $G_PATH);

//require_once G_PATH . "common/config/config.php";
require_once G_PATH . "common/config/params.php";

require_once G_PATH . "common/utils.php";

require_once G_PATH . "common/mail.php";

require_once G_PATH . "common/Classes/EntityController.php";


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
//202412logsMyPC ob_clean();
