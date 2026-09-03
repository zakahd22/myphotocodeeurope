<?php
require("common.php");


if(!$APP_user) return;

//estats 0: en edició   1: publica  2: antiga
//SELECT `id`, `url`, `estat` FROM `App_news` WHERE estat = 1

//NOTA: i si ja s'ha descarregat a un altre dispositiu??

//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//SELECT `id`, `rental_id`, `start_date`, `title`, `background_id`, `private`, `autocreated`, `ftp_folder_id`, `available` FROM `events` WHERE 1

//$sql = "SELECT `idPhoto`,`start_date`,code FROM (`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
//$sql.= " INNER JOIN events ON photos.event_id = events.id WHERE idUser='$APP_userId' AND downloaded = 0;";
$sql = "SELECT `idPhoto`,code FROM `Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id ";

//20130531shared  $sql.= " WHERE idUser=$APP_userId AND downloaded = 0;";
$sql.= " WHERE idUser=$APP_userId AND automaticallyShared = 1;";//20130531shared  

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}
//'REMOTE_HOST'
$url =  APP_curPageURL();
$xml = $APP_xmlOKcomm;
$xml.= "<photos>";
while($APP_BdD->FetchRs()){
    $xml.= "<photo>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<photo_id>$tmp</photo_id>";
//    $startdate =  $APP_BdD->GetField(2);
    $code =  $APP_BdD->GetField(2);
    $xml.= "<photo_url>{$url}photomobile/$code.jpg</photo_url>";
    $xml.= "</photo>";
}
$APP_BdD->CloseRs();
$xml.= "</photos>";


echo "$APP_xml$xml</return>"; // no cal res més



?>
