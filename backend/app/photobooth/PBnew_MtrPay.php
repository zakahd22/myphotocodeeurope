<?php
$APP_startSession = true;

$IsPDnew = 1;
require("common.php");


if(!$APP_dongleOK) return;

//            $resp = PB_send("checkp.php","ctrl=$MtrControl&id={$this->dict_offerOrder->order_id}&m={$this->dict_offerOrder->total_price}");

if(!isset($_REQUEST['id'])){
    APP_fesLog("Error - code 01 in PBnew_MtrPay.");
    echo "ko#01";
    return;
}
if(!isset($_REQUEST['m'])){
    APP_fesLog("Error - code 02 in PBnew_MtrPay.");
    echo "ko#02";
    return;
}
if(!isset($_REQUEST['idm'])){
    APP_fesLog("Error - code 03 in PBnew_MtrPay.");
    echo "ko#03";
    return;
}
$idMtrOrder = $_REQUEST['id'];
$total = $_REQUEST['m'];
$idMtr = $_REQUEST['idm'];

$MtrControl = rndm32(15);

//SELECT `idOrder`, `idPB`, `idMtrOrder`, `idMtr`, `when`, `total`, `currency`, `MtrDescr`, `MtrControl`, `fpag`, `fpagcontrol`, `fpagn`, `fpagstatus` FROM `Mtr_orders` WHERE 1

    $sql = "INSERT INTO Mtr_orders SET idPB=$APP_idBooth, idMtrOrder=$idMtrOrder, idMtr=$idMtr, `when`=$APP_araTimeSerial, `total`=$total, MtrControl='$MtrControl';";//20130502

    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        APP_fesLog("Error - code 04 in PBnew_MtrPay: $sql.");
        echo "ko#04";
        return;
    }


        echo "ok#$MtrControl";


?>
