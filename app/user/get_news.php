<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_user) return;

//estats 0: en edició   1: publica  2: antiga
//SELECT `id`, `url`, `estat` FROM `App_news` WHERE estat = 1

$sql = "SELECT `id`, `url`, `urlMobile` FROM `App_news` WHERE estat = 1 AND type = 2 ORDER BY id ; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

$xml = $APP_xmlOKcomm;
$xml.= "<newsgroup>";
while($APP_BdD->FetchRs()){
    $xml.= "<news>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<news_id>$tmp</news_id>";
    $tmp =  $APP_BdD->GetField(2);
    $xml.= "<news_url>$tmp</news_url>";
    $tmp =  $APP_BdD->GetField(3);
    $xml.= "<news_urlMobile>$tmp</news_urlMobile>";
    $xml.= "</news>";
}
$APP_BdD->CloseRs();
$xml.= "</newsgroup>";
//<newsgroup>
//<news_>
//<news_id>8</news_id>
//<news_url>url</news_url>
//</news>
//<news_>
//<news_id>6</news_id>
//<news_url>url</news_url>
//</news>
//</newsgroup>


//20130529 INICI donar per finalitzades les notificacions de news
//comprovem si ja hi ha una activa SELECT `id`, `idUser`, `idDoc`, `typeNot`, `when`, `estat` FROM `Appusr_userNot` WHERE 1
$sql = "SELECT id FROM Appusr_userNot WHERE idUser = $APP_userId AND `typeNot` = 1 AND `estat` < 2;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
    return;
}
$calTreure = false;
if($APP_BdD->FetchRs()){
    $calTreure = true;
}
$APP_BdD->CloseRs();

if($calTreure){
    $sql = "UPDATE Appusr_userNot SET estat=2 WHERE idUser = $APP_userId AND typeNot = 1;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        echo "Error - Common alertOffline - code 02 .";
        $APP_common_error = true;
        return;

    }
    //badge !!!!!!!! 
    $sql = "SELECT id FROM Appusr_userNot WHERE idUser = $APP_userId AND estat<2;";
    //NOTA: com que només controlem news, serà zero
    
    
echo $APP_okResp;//20170220apns
//20170220apns
//    include("../easyapns/src/php/APP_apns.php");
//     APNS_setBadgeUser($APP_userId,0);
//    
//    $APP_okResp = "$APP_xml$xml</return>";
//    ignore_user_abort(true);
//    header("Connection: close");
//    header("Content-Length: " . mb_strlen($APP_okResp));
//    echo $APP_okResp;
//    flush();    
//    APNS_sendMessages();


   
}
else
//20130529 FINAL
echo "$APP_xml$xml</return>";



?>
