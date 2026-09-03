<?php
require("common.php");


if(!$APP_user) return;


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//"- URL a user image
//- número de fotos
//- número de votos
//- e-mail
//- URL a imagen QR pequeña
//- publicación automática en facebook (si/no)
//- publicación automática en el muro (si/no)
//- envío automático a e-mail del perfil (si/no)"

$nPhotos = 0;
$sql = "SELECT COUNT(*) FROM Appusr_userPhoto WHERE idUser=$APP_userId; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}
if($APP_BdD->FetchRs()){
   $nPhotos =  $APP_BdD->GetField(1);
}
$APP_BdD->CloseRs();

$nVotes = 0;
$sql = "SELECT SUM(votes) FROM Appusr_userPhoto WHERE idUser=$APP_userId; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
    return;
}
if($APP_BdD->FetchRs()){
   $nVotes =  $APP_BdD->GetField(1);
}
$APP_BdD->CloseRs();

//$sql = "SELECT `idPhoto`,`start_date`,code FROM (`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
//$sql.= " INNER JOIN events ON photos.event_id = events.id WHERE idUser='$APP_userId' AND downloaded = 0;";
$sql = "SELECT email, `autofcbk`, `autowall`, `autoemail` FROM `Appusr_user` ";
$sql.= " WHERE id=$APP_userId;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0003 </comm_status></return>";
    return;
}
//'REMOTE_HOST' 
$url =  APP_curPageURL();
$xml = $APP_xmlOKcomm;
$xml.= "<user>";
if($APP_BdD->FetchRs()){
 //20121027    $xml.= "<userimage>{$url}userimage/img$APP_userId.jpg</userimage>";
    
   
 //20131028  $fitxerImatge = "userimage/img$APP_userId.jpg";
   $fitxerImatge = "userimage/img$APP_userQR.jpg"; //20131028
   
    if(!file_exists($fitxerImatge)) $fitxerImatge = "userimage/avatar.png";//20121027
    $xml.= "<userimage>{$url}$fitxerImatge</userimage>";//20121027
    
    $xml.= "<nphotos>$nPhotos</nphotos>";
    $xml.= "<nvotes>$nVotes</nvotes>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(1));
    $xml.= "<email>$tmp</email>";
//20131028    $xml.= "<userqr>{$url}userqr/qr$APP_userId.png</userqr>";
    $xml.= "<userqr>{$url}userqr/qr$APP_userQR.png</userqr>";//20131028
    $tmp =  $APP_BdD->GetField(2);
    if($tmp) $tmp = "si"; else $tmp = "no";
    $xml.= "<autofcbk>$tmp</autofcbk>";
    $tmp =  $APP_BdD->GetField(3);
    if($tmp) $tmp = "si"; else $tmp = "no";
    $xml.= "<autowall>$tmp</autowall>";
    $tmp =  $APP_BdD->GetField(4);
    if($tmp) $tmp = "si"; else $tmp = "no";
    $xml.= "<autoemail>$tmp</autoemail>";
}
$APP_BdD->CloseRs();
$xml.= "</user>";


echo "$APP_xml$xml</return>"; // no cal res més



?>
