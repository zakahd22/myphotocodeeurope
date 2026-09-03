<?php
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

require_once G_PATH . "common/config/config.php";
require_once G_PATH . "common/utils.php";
require_once G_PATH . "common/mail.php";

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

if(isset($srv)){
    switch ($srv) {
        case 'myphotocode.com':
            define("G_EMP", 'MyPhotoCode');
            define("G_PAGE", 'https://www.myphotocode.com/');
            define("G_BUSINESS_ID", 1);
            define("G_BUSINESS", 'jtarres@dc-image.com');
            define("G_RENTNAME", 'myphotocode');
            define("G_TEST", '');

    //May not Work-------------
            //Variables
            $URL = "https://www.myphotocode.com/";
            $URL_LOGIN = "https://myphotocode.com/";
            $URL_SAND = "https://myphotocode.com/";
            $folder2_ = "/";

            //COUNTPAGES
            $LIMITERPAGES=16;
            $LIMIT=16;
            $PAGE= 1;

            //COUNTPAGES POPUP
            $LIMITERPAGES_POPUP=18;
            $LIMIT_POPUP=18;
            $PAGE_POPUP= 1;

            $BOOTHS_TYPE_STATUS = array('Production','Finished Factory Product(Stock)','Stock','Sold','Returned' , "Returned & Damaged" , "Damaged" , "Incomplete" , "Refurbished");

            date_default_timezone_set("Europe/Madrid");
    //---------------------------

            $_SESSION['COUNTRY'] = 'US';
            error_reporting(0);
            ini_set('display_errors', 0);
            break;

        default:
            define("G_PAGE", 'https://127.0.0.1/myphotocode/');
            define("G_BUSINESS", 'aleix@dc-image.com');
            define("G_TEST", '');

    //May not Work---------------
           //Variables
            $G_PAGE = G_PAGE;
            $URL = G_PATH;
            $URL_LOGIN = G_PATH;
            $URL_SAND = G_PATH;
            $folder2_ = "/";

            //COUNTPAGES
            $LIMITERPAGES=16;
            $LIMIT=16;
            $PAGE= 1;

            //COUNTPAGES POPUP
            $LIMITERPAGES_POPUP=18;
            $LIMIT_POPUP=18;
            $PAGE_POPUP= 1;

            $BOOTHS_TYPE_STATUS = array('Production','Finished Factory Product(Stock)','Stock','Sold','Returned' , "Returned & Damaged" , "Damaged" , "Incomplete" , "Refurbished");

            date_default_timezone_set("Europe/Madrid");
    //---------------------------

            // Only select one (for the moment always US)
            $_SESSION['COUNTRY'] = 'US';
            define("G_BUSINESS_ID", 1);
            define("G_EMP", 'MyPhotoCode');
            define("G_RENTNAME", 'myphotocode');

    //        $_SESSION['COUNTRY'] = 'ES';
    //        define("G_BUSINESS_ID", 2);
    //        define("G_EMP", 'Alquila Fotomatón');
    //        define("G_RENTNAME", 'alquila-fotomaton');

    //        $_SESSION['COUNTRY'] = 'ES';
    //        define("G_BUSINESS_ID", 3);
    //        define("G_EMP", 'Eventos con imagen');
    //        define("G_RENTNAME", 'eventos-con-imagen');

            error_reporting(1);
            ini_set('display_errors', E_ALL);
            break;
    }
    
    $_SESSION['RENT_NAME'] = G_RENTNAME;
    define("G_ASSETS", 'assets/');
    $_SESSION['ASSETS'] = G_ASSETS;
    define("G_HTML", 'html/');
    $_SESSION['HTML'] = G_HTML;

    define("G_ASSETS_PATH", G_PATH . G_ASSETS);
    $_SESSION['ASSETS_PATH'] = G_ASSETS_PATH;
    $_SESSION["IMG_PATH"] = $_SESSION['ASSETS']."img/" . $_SESSION['LANG'] . "/";
}