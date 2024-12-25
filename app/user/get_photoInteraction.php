<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_user) return;


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
// OLD      SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `idUserVoting`, `idUserVoted`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//params:
//"- username
//- password
//- ID de la foto

//resposta:
//"- URL a imagen tamaño móvil
//- estado del voto (es por usuario y foto)"

//20121023 
//if(isset($_REQUEST['id'])){ $idPhoto = $_REQUEST['id'];}
//else{
//echo "$APP_xml<comm_status>Error - No ID photo param</comm_status></return>";
//return;
//}
if(isset($_REQUEST['photo_id'])){ $idPhoto = $_REQUEST['photo_id'];}
else{
echo "$APP_xml<comm_status>Error - No ID photo param</comm_status></return>";
return;
}
if(isset($_REQUEST['user_id'])){ $idUser = $_REQUEST['user_id'];}
else{
echo "$APP_xml<comm_status>Error - No ID user param</comm_status></return>";
return;
}

//anem per parts, primer info de la foto (Appusr_userPhoto)
//'REMOTE_HOST'
$url =  APP_curPageURL();
$xml = $APP_xmlOKcomm;


$sql = "SELECT photos.code FROM `Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id ";
$sql.= " WHERE Appusr_userPhoto.idUser=$idUser AND Appusr_userPhoto.idPhoto=$idPhoto ";//20121023

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}


$xml.= "<photo>";
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<photo_url>{$url}photomobile/$tmp.jpg</photo_url>";
}else{
    $sql = "SELECT code FROM photos WHERE id = $idPhoto";
    $esOK = $APP_BdD->OpenRs($sql);
    if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<photo_url>{$url}photomobile/$tmp.jpg</photo_url>";
    }
}
$APP_BdD->CloseRs();


//i ara del vot Appusr_userVotes
//SELECT `idUserVoting`, `idUserVoted`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
$sql = "SELECT datetime FROM Appusr_userVotes ";
$sql.= " WHERE idUserVoting=$APP_userId AND idUserVoted=$idUser AND idPhoto=$idPhoto; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
return;
}




if($APP_BdD->FetchRs()){
    $datetime = $APP_BdD->GetFieldDateTime(1);
    if($datetime){//vol dir que el va votar
        $xml.= "<voted>si</voted><date_voted>";
        $xml.= $datetime->format("m-d-Y H:i");
        $xml.= "</date_voted>";
    }
    else $xml.= "<voted>no</voted><date_voted></date_voted>";
}
else $xml.= "<voted>no</voted><date_voted></date_voted>";
$APP_BdD->CloseRs();

if(file_exists("./photo3D/" .  $tmp . "-T13.jpg")){
    $xml.= "<dim>yes</dim>";
}else{
    $xml.= "<dim>no</dim>";
}

$xml.= "</photo>";


echo "$APP_xml$xml</return>"; // no cal res més


?>
