<?php
require("common.php");
header('Content-Type: image/jpeg');


if(!$APP_dongleOK) return;


if(!isset($_REQUEST['id'])){
    echo "Error2 - code 01";
    return;
}
$idPhoto = $_REQUEST['id'];


if(!isset($_REQUEST['code'])){
    echo "Error2 - code 02";
    return;
}
$code = $_REQUEST['code'];


$sql = "SELECT events.start_date,events.id FROM photos LEFT JOIN events ON photos.event_id = events.id WHERE photos.id=$idPhoto AND code='$code';";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "Error2 - code 03";
    return;
}



if($APP_BdD->FetchRs()){
   $photoDir = "../../events/".$APP_BdD->GetField(1);
   $photoDir.=  $APP_BdD->GetField(2);
}
else{
    $APP_BdD->CloseRs();
    echo "$APP_xml<comm_status>Error - Code not found $sql</comm_status></return>";
    return;
}

$APP_BdD->CloseRs();

$nomFoto = "$photoDir/$code.jpg";

$im = imagecreatefromjpeg($nomFoto);

imagejpeg($im);
imagedestroy($im);




?>
