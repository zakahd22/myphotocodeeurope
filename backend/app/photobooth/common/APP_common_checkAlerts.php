<?php
//es tracta de comprovar si un booth ha de tenir estat a zero
//App_boothAlert money alerts, Stock alerts
//offline alert
$APP_common_error = false;
$sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND estat<2;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    echo "Error - Common checkAlerts - code 01 $sql";
    $APP_common_error = true;
    return;
}
$estat = 0;
$APP_common_badge = $APP_BdD->GetRsRows();
if($APP_BdD->FetchRs()){
    $estat = 1;
    $APP_common_badge = $APP_BdD->GetRsRows();
}
$APP_BdD->CloseRs();

$sql = "UPDATE App_booths SET estat=$estat WHERE idBooth = $APP_idBooth";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    echo "Error - Common checkAlerts - code 02 $sql.";
    $APP_common_error = true;
    return;

}
?>
