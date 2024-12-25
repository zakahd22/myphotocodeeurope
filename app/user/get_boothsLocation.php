<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_user) return;


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//params:
//"- geoposicionamiento del dispositivo
//- zona de visualización"


//resposta:
//"Lista de booths dentro del area que contiene
//- booth name
//- dirección
//- si utiliza formato Gian Strip (si/no)"



if(isset($_REQUEST['latitude'])){ $latitude = $_REQUEST['latitude']; $latitude*=1000000;}
else{
echo "$APP_xml<comm_status>Error - No latitude param</comm_status></return>";
return;
}

if(isset($_REQUEST['longitude'])){ $longitude = $_REQUEST['longitude']; $longitude*=1000000;}
else{
echo "$APP_xml<comm_status>Error - No longitude param</comm_status></return>";
return;
}

if(isset($_REQUEST['delta'])){ $delta = $_REQUEST['delta']; $delta*=1000000;}
else{
echo "$APP_xml<comm_status>Error - No delta param</comm_status></return>";
return;
}

$latitudeMin = $latitude - $delta;
$latitudeMax = $latitude + $delta;
$longitudeMin = $longitude - $delta;
$longitudeMax = $longitude + $delta;

$sql = "SELECT App_booths.idBooth, App_booths.type, App_booths.name,`location`, App_booths.latitude, App_booths.longitude
    FROM App_booths WHERE (latitude BETWEEN $latitudeMin AND $latitudeMax) AND (longitude BETWEEN $longitudeMin AND $longitudeMax); ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

$xml = $APP_xmlOKcomm;
$xml.= "<booths>";
while($APP_BdD->FetchRs()){
    $xml.= "<booth>";
    $idBooth =  $APP_BdD->GetField(1);
    $xml.= "<booth_id>$idBooth</booth_id>";
    $tmp = $APP_BdD->GetField(2); 
    if($tmp == "C") $xml.= "<mega>si</mega>";
    else $xml.= "<mega>no</mega>";
    $tmp = APP_preparaXML($APP_BdD->GetField(3));
    $xml.= "<booth_name>$tmp</booth_name>";
    $tmp = APP_preparaXML($APP_BdD->GetField(4));
    $xml.= "<location>$tmp</location>";
    $tmp = $APP_BdD->GetField(5); if($tmp) $tmp/=1000000;
    $xml.= "<latitude>$tmp</latitude>";
    $tmp = $APP_BdD->GetField(6); if($tmp) $tmp/=1000000;
    $xml.= "<longitude>$tmp</longitude>";
    $xml.= "</booth>";

}
$APP_BdD->CloseRs();
$xml.= "</booths>";


echo "$APP_xml$xml</return>"; // no cal res més


?>
