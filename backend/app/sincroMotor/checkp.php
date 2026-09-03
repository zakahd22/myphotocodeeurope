<?php
require("common.php"); 


//if(!$APP_dongleOK) return;

//            $resp = PB_send("checkp.php","ctrl=$MtrControl&id={$this->dict_offerOrder->order_id}&m={$this->dict_offerOrder->total_price}&idm=1");

if(!isset($_REQUEST['ctrl'])){
    fesLog("Error - code 01 in checkp.");
    return;
}
if(!isset($_REQUEST['m'])){
    fesLog("Error - code 02 in checkp.");
    return;
}
if(!isset($_REQUEST['idm'])){
    fesLog("Error - code 03 in checkp.");
    return;
}
//$idMtrOrder = $_REQUEST['id'];
$MtrControl = $_REQUEST['ctrl'];
$total = $_REQUEST['m'];
$idMtr = $_REQUEST['idm'];

$idMtrOrder = "";
//SELECT `idOrder`, `idPB`, `idMtrOrder`, `idMtr`, `when`, `total`, `currency`, `MtrDescr`, `MtrControl`, `fpag`, `fpagcontrol`, `fpagn`, `fpagstatus` FROM `Mtr_orders` WHERE 1
    //$sql = "INSERT INTO Mtr_orders SET idPB=$APP_idBooth, idMtrOrder=$idMtrOrder, idMtr=$idMtr, `when`=$APP_araTimeSerial, `total`=$total, ,MtrControl='$MtrControl';";//20130502

 //!!!   $sql = "SELECT idMtrOrder FROM Mtr_orders WHERE idMtrOrder=$idMtrOrder AND idMtr=$idMtr AND MtrControl='$MtrControl';";//20130502
    $sql = "SELECT idMtrOrder FROM Mtr_orders WHERE idMtr=$idMtr AND MtrControl='$MtrControl';";//20130502
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
       fesLog("Error - code 04 in checkp: $sql.");
        return;

}
if($APP_BdD->FetchRs()){
    $idMtrOrder = $APP_BdD->GetField(1);
}
$APP_BdD->CloseRs();

echo $idMtrOrder;

if(!$idMtrOrder){
    echo "ko#$sql";
}

else{
    $sql = "UPDATE Mtr_orders SET totalMtr=$total WHERE idMtr=$idMtr AND MtrControl='$MtrControl';";
     $esOK = $APP_BdD->Execute($sql);
}
?>
