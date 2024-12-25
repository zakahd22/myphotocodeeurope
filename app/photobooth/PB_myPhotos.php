<?php
$APP_common_no_idb = true;//20140626
require("common.php");


if(!$APP_dongleOK) return;


if(!isset($_REQUEST['code'])){
    echo "Error2 - code 01";
    return;
}

$codi = $_REQUEST['code'];
$l = strlen($codi);
if($l <= 12){
    echo "Error2 - code 02";
    return;
}
$l-=12;
$idUser = substr($codi, 0,$l);


//primer l'usuari
//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1

$sql = "SELECT `username`, `email` FROM `Appusr_user` WHERE id=$idUser AND qrcode = '$codi';";
//NOTA: en el futur s'enviarà un email d'informació del login
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
    echo "Error2 - code 03 $sql";
    return;
}
$idUserOK = false;
if($APP_BdD->FetchRs()){
    $idUserOK = true;
    $username = $APP_BdD->GetField(1);
    $email = $APP_BdD->GetField(2);
}
$APP_BdD->CloseRs();


//echo "TRACE $sql";
//return;

if(!$idUserOK){
    echo "Error2 - code 03bis";
    return;
}

//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//SELECT `id`, `rental_id`, `start_date`, `title`, `background_id`, `private`, `autocreated`, `ftp_folder_id`, `available` FROM `events` WHERE 1

//$sql = "SELECT `idPhoto`,`start_date`,code FROM (`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
//$sql.= " INNER JOIN events ON photos.event_id = events.id WHERE idUser='$APP_userId' AND downloaded = 0;";
$sql = "SELECT `idPhoto`,code FROM `Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id ";
//$sql.= " INNER JOIN events ON photos.event_id = events.id WHERE idUser='$APP_userId' AND downloaded = 0;";
$sql.= " WHERE idUser=$idUser ORDER BY Appusr_datetime DESC;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "Error2 - code 04 $sql";
    return;
}
//'REMOTE_HOST'
//$url =  APP_curPageURL();
//$xml = $APP_xmlOKcomm;
//$xml.= "<photos>";

$ret = "";
$retFotos = "";
$nFotos = 0;

while($APP_BdD->FetchRs()){
    $idPhoto =  $APP_BdD->GetField(1);
    $code =  $APP_BdD->GetField(2);
    $retFotos.= "|$idPhoto|$code";
    $nFotos++;
}
$APP_BdD->CloseRs();

echo "OK|$username|$nFotos".$retFotos; // no cal res més



?>
