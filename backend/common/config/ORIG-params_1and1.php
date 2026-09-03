<?php
    define("G_EMP", 'MyPhotoCode');
    define("G_PAGE", 'www.myphotocode.com/');
    
    define("G_CTRL_ES_PAGE", 'www.control.alquilafotomaton.es/');
    define("G_MTR_ES_PAGE", 'www.alquilafotomaton.es/');
    define("G_CTRL_US_PAGE", 'www.control.bestrentalphotobooth.com/');
    define("G_MTR_US_PAGE", 'www.bestrentalphotobooth.com/');
    
    define("G_BUSINESS_ID", 1);
    define("G_BUSINESS", 'jtarres@dc-image.com');
    define("G_RENTNAME", 'myphotocode');
    define("G_TEST", '0');

    //Variables
    $G_PAGE = G_PAGE;
    $URL = G_PATH;
    $URL_LOGIN = G_PATH;
    $URL_SAND = G_PATH;
    $folder2_ = "/";

    //COUNTPAGES
    $LIMITERPAGES = 16;
    $LIMIT = 16;
    $PAGE = 1;

    //COUNTPAGES POPUP
    $LIMITERPAGES_POPUP = 18;
    $LIMIT_POPUP = 18;
    $PAGE_POPUP = 1;

    $BOOTHS_TYPE_STATUS = array('Production','Finished Factory Product(Stock)','Stock','Sold','Returned' , "Returned & Damaged" , "Damaged" , "Incomplete" , "Refurbished");

    $mail_noreply = array(
        "host" => "smtp.1and1.com",
        "port" => 25,
        "smtp_user" => "noreply@myphotocode.com",
        "smtp_pass" => "DC12345", //substituïr la "i" del "mail" per un "1"
        "sendmail" => 1

    );
    
    date_default_timezone_set("Europe/Madrid");

    $_SESSION['COUNTRY'] = 'US';
    error_reporting(0);
    ini_set('display_errors', 0);
