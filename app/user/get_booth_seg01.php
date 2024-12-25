<?php
$APP_open = true;
require("common.php");


if(!$APP_user) return;

if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
else{
echo "$APP_xml<comm_status>Error - No id param</comm_status></return>";
return;
}


$sql = "SELECT App_booths.type, Appusr_userPhoto.idUser,`Appusr_userPhoto`.`idPhoto`, photos.code, `username`, `title`,`location`, `latitude`, `longitude`, `Appusr_datetime`, `votes` ";
$sql.= " FROM ((`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
$sql.= " INNER JOIN App_booths ON Appusr_userPhoto.idBooth = App_booths.idBooth )";
$sql.= " INNER JOIN Appusr_user ON Appusr_userPhoto.idUser = Appusr_user.id";
$sql.= " WHERE wall=1 AND App_booths.idBooth=$idBooth ORDER BY `Appusr_datetime` DESC;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}
//'REMOTE_HOST'
$url =  APP_curPageURL();
$xml = $APP_xmlOKcomm;

$ambType = false;

$xml.= "<booth>";


while($APP_BdD->FetchRs()){
    if(!$ambType){
        $tmp =  $APP_BdD->GetField(1);
        $xml.= "<booth_url>{$url}boothimage/$tmp.png</booth_url>";
        $ambType = true;
        $xml.= "<photos>";
    }
    
    //echo "$APP_xml$xml<TRACE>TRACE</TRACE></return>";
    
    
    $xml.= "<photo>";
    $tmp =  $APP_BdD->GetField(2);
    $xml.= "<user_id>$tmp</user_id>";
    $tmp =  $APP_BdD->GetField(3);
    $xml.= "<photo_id>$tmp</photo_id>";
    $code =  $APP_BdD->GetField(4);
    $xml.= "<photo_url>{$url}photowall/$code.jpg</photo_url>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(5));
    $xml.= "<username>$tmp</username>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(6));
    $xml.= "<title>$tmp</title>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(7));
    $xml.= "<location>$tmp</location>";
    $tmp =  $APP_BdD->GetField(8); $tmp/=1000000;
    $xml.= "<latitude>$tmp</latitude>";
    $tmp =  $APP_BdD->GetField(9); $tmp/=1000000;
    $xml.= "<longitude>$tmp</longitude>";

    $datetime = $APP_BdD->GetFieldDateTime(10);
    $xml.= "<date>";
    if($datetime){
     $xml.= $datetime->format("m-d-Y H:i");
    }
    $xml.= "</date>";
    $tmp =  $APP_BdD->GetField(11);
    $xml.= "<nvotes>$tmp</nvotes>";
   
    $xml.= "</photo>";
}
$APP_BdD->CloseRs();
if($ambType) $xml.= "</photos>";
$xml.= "</booth>";


echo "$APP_xml$xml</return>"; // no cal res més



?>
