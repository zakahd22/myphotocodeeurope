<?php
$APP_startSession = true;

//20140708 INICI
if($APP_common_error){
    APP_fesLog("Error20140708 in PB_Start.");
    return;
}
//20140708 FINAL
require("common.php");


if(!$APP_dongleOK) return;

if(isset($_REQUEST['ccsn']))//
{
    
    $sql = "UPDATE App_booths SET cardReaderSN ='{$_REQUEST['ccsn']}' WHERE idBooth=$APP_idBooth;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        echo "Error - Database update: $sql.";
        return;

    }//SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_boothDongle`

}



    //20130524 INICI continuem amb send messages de APNS després de contestar (aqui sempre s'ha insertat un missatge
    //20130524 echo $APP_okResp;

echo $APP_okResp;//20170220apns
////20170220apns
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
