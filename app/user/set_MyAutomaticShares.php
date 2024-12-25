<?php
require("common.php");

if(!$APP_user) return;

//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//params:
//"- username
//- password
//- code (igual que get_photo)

if(isset($_REQUEST['code'])){ $code = $_REQUEST['code'];}
else{
echo "$APP_xml<comm_status>Error - No code param</comm_status></return>";
return;
}


$sql = "SELECT photos.id FROM photos WHERE code='$code';";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}

if($APP_BdD->FetchRs()){
   $idPhoto =  $APP_BdD->GetField(1);
}
else{
    $APP_BdD->CloseRs();
    echo "$APP_xml<comm_status>Error - Code not found </comm_status></return>";
    return;
}

$APP_BdD->CloseRs();

//actualitzem
$sql = "UPDATE Appusr_userPhoto SET automaticallyShared = 2 WHERE idUser=$APP_userId AND idPhoto=$idPhoto;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}

echo "$APP_xml$APP_xmlOKcomm</return>"; // no cal res més
?>
