<?php

/*
 * Petició des d'un PB de pagament per PayPal
 * Params dongle i PB, etc
 * ppm: money to pay
 * ppc: currency
 * 
 */

if(isset($_REQUEST['ppc'])){
    $currency = $_REQUEST['ppc'];
}
else{
    echo "ko#error pp01 no curr";
    return;
}
if(isset($_REQUEST['ppm'])){
    $total = $_REQUEST['ppm'];
}
else{
    echo "ko#error pp02 no money";
    return;
}

require("common.php");


if(!$APP_dongleOK) return;
        
//dades de pagament al PB
$sql ="SELECT `payPalVendor` FROM `App_booths` WHERE  idBooth=$APP_idBooth;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    echo "ko#error pp03 $sql";
    return;
}
$calInsert = true;
if($APP_BdD->FetchRs()){
    $payPalVendor = $APP_BdD->GetField(1);
    $calInsert = false;
}
$APP_BdD->CloseRs();

if(!$payPalVendor){
    echo "ok####";
    return;
}

//busquem si ja existeix l'ordre
$sql ="SELECT idOrder FROM `App_PBorders` WHERE  `idPB` = $APP_idBooth AND `fpagstatus` = 0;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    echo "ko#error pp04 $sql";
    return;
}
$idOrder = "";
if($APP_BdD->FetchRs()){
    $idOrder = $APP_BdD->GetField(1);
}
$APP_BdD->CloseRs();

$ara = new DateTime("now");
$when = $APP_BdD->myDateTimeSerial($ara);

$fpagcontrol = rndm32(20);
$controlcomanda = ", `fpagcontrol` = '$fpagcontrol'";

if($idOrder){
    $sql = "UPDATE App_PBorders SET `when`=$when, `total`=$total, `currency`= '$currency', `payPalVendor`='$payPalVendor' $controlcomanda WHERE idOrder = $idOrder;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        echo "ko#error pp05 $sql";
     return;
    }
}
else{
    $sql = "INSERT INTO App_PBorders SET `idPB` = $APP_idBooth, `when`=$when, `total`=$total, `currency`= '$currency', `payPalVendor`='$payPalVendor' $controlcomanda ;";
    $idOrder = $APP_BdD->ExecuteInsert($sql);
    if(!$idOrder) {
        echo "ko#error pp06 $sql";
        return;
    }
}
//$xmlcontrolcomanda.= "<pay_url>https://www.myphotocode.com/app/pay/OrderPal?p=$fpagcontrol$idOrder</pay_url>";


echo "ok#$fpagcontrol$idOrder#https://www.myphotocode.com/app/pay/PBPal?p=$fpagcontrol$idOrder";


?>
