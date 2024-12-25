<?php

$IsPDnew = 1;
$APP_common_isAlive = 1; //201708
require("common.php");

//20170626deu INICI
if(isset($_REQUEST['idb'])){
    if($_REQUEST['idb']==7682){
        APP_fesLogDebbug("PBnew_Alive de 7682 *******************","logDebug20170626deu");
    }
}
//20170626deu FINAL


//20140708 INICI
if($APP_common_error){
    APP_fesLog("Error20140708 in PB_Alive.");
    return;
}
//20140708 FINAL


if(!$APP_dongleOK) return;


    //20130524 INICI continuem amb send messages de APNS després de contestar (aqui sempre s'ha insertat un missatge
    //20130524 echo $APP_okResp;

echo $APP_okResp;//20170220apns
//20170220apns
//    if($APNS_MessageAdded){
//        ignore_user_abort(true);
//        header("Connection: close");
//        header("Content-Length: " . mb_strlen($APP_okResp));
//        echo $APP_okResp;
//        flush();    
//        APNS_sendMessages();
//    }
//    else{
//        echo $APP_okResp;
//    }
    //20130524 FINAL 


?>
