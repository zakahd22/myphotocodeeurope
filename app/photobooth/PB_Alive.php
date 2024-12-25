<?php
require("common.php");

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
