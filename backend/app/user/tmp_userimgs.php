<?php


 
require("../common/APP_BdD.php");
$sql = "SELECT id,qrcode FROM Appusr_user ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$Error Database error code: 0001";
return;
}

while($APP_BdD->FetchRs()){
    $id = $APP_BdD->GetField(1);
    $codi = $APP_BdD->GetField(2);
    rename("userimage/img$id.jpg", "userimage/img$codi.jpg");
    rename("userqr/qr$id.png", "userqr/qr$codi.png");
}
$APP_BdD->CloseRs();

?>
