<?php
require("common.php");


if(!$APP_user) return;

//estats 0: en edició   1: publica  2: antiga
//SELECT `id`, `url`, `estat` FROM `App_news` WHERE estat = 1

//20121025   $sql = "SELECT `id`, `url`, `urlMobile` FROM `App_news` WHERE estat = 1 ORDER BY id DESC; ";
$sql = "SELECT `id`, `url`, `urlMobile` FROM `App_news` WHERE estat = 1 AND type = 1 ORDER BY id ; ";//20121025   
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


echo "$APP_xml$xml</return>"; // no cal res més



?>
