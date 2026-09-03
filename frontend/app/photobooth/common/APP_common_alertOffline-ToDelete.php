<?php
$APP_common_error = false;
//com que acabem de rebre info, cal desactivar qualsevol alerta offline del booth
$sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = 1 AND estat<2;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    echo "Error - Common alertOffline - code 01 $sql";
    $APP_common_error = true;
    return;
}
$calTreure = false;
if($APP_BdD->FetchRs()){
    $calTreure = true;
}
$APP_BdD->CloseRs();

if($calTreure){
    $sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = 1;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        echo "Error - Common alertOffline - code 02 $sql.";
        $APP_common_error = true;
        return;

    }
    //cal actualitzar l'estat del booth
    include 'APP_common_checkAlerts.php';
    if($APP_common_error) return;
    
}



?>
