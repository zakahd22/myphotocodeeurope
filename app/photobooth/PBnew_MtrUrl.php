<?php

if(isset($_REQUEST['idb'])){ $idb = $_REQUEST['idb'];} else {$idb = "";}



$laUrl = "ok#https://www.bestrentalphotobooth.com/photobooth/logout.php";
//$laUrl = "ok#https://www.bestrentalphotobooth.com/photobooth";

if($idb == 12){//sala DC
 $laUrl = "ok#https://pre.bestrentalphotobooth.com/photobooth/logout.php";
 //$laUrl = "ok#https://www.bestrentalphotobooth.com/photobooth/logout.php";
   
}
//$idb == 6487){//DCA 2n STRIP a DB AB6Z

//if($idb == 482){//sala DCA a instalar a David's B.
// $laUrl = "ok#https://pre.bestrentalphotobooth.com/photobooth/logout.php";
//   
//}

if($idb == 7101){//portàtil
$laUrl = "ok#https://pre.bestrentalphotobooth.com/photobooth/logout.php";
   
}

PB_fesLog("PBnew_MtrUrl, id: $idb, laUrl: $laUrl", 'logMtrUrl.dat');
echo $laUrl;




function PB_rndm32($len) {
    $base32_table = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z","2","3","4","5","6","7","8","9");
    $out = "";
    for($i=0;$i<$len;$i++){
        $out.= $base32_table[rand(0,31)];
    }
    return $out;
}

function PB_fesLog($text, $dir='logPB.dat') 
{
    if(filesize($dir) > 5000000){
        rename( $dir , "logPB.".rndm32(3).".bak" );    
        $fh = fopen($dir, 'w');
    }
    else $fh = fopen($dir, 'a');
    fwrite($fh, date('Y-m-d H:i:s ') . $text."\r");
    fclose($fh);
}



//echo "ok#https://www.bestrentalphotobooth.com/photobooth";
//de moment sense cap control
//$APP_startSession = true;
//
//$IsPDnew = 1;
//require("common.php");
//
//
//if(!$APP_dongleOK) return;
//
////            $resp = PB_send("checkp.php","ctrl=$MtrControl&id={$this->dict_offerOrder->order_id}&m={$this->dict_offerOrder->total_price}");
//
//if(!isset($_REQUEST['id'])){
//    APP_fesLog("Error - code 01 in PBnew_MtrPay.");
//    echo "ko#01";
//    return;
//}
//if(!isset($_REQUEST['m'])){
//    APP_fesLog("Error - code 02 in PBnew_MtrPay.");
//    echo "ko#02";
//    return;
//}
//if(!isset($_REQUEST['idm'])){
//    APP_fesLog("Error - code 03 in PBnew_MtrPay.");
//    echo "ko#03";
//    return;
//}
//$idMtrOrder = $_REQUEST['id'];
//$total = $_REQUEST['m'];
//$idMtr = $_REQUEST['idm'];
//
//$MtrControl = rndm32(15);
//
////SELECT `idOrder`, `idPB`, `idMtrOrder`, `idMtr`, `when`, `total`, `currency`, `MtrDescr`, `MtrControl`, `fpag`, `fpagcontrol`, `fpagn`, `fpagstatus` FROM `Mtr_orders` WHERE 1
//
//    $sql = "INSERT INTO Mtr_orders SET idPB=$APP_idBooth, idMtrOrder=$idMtrOrder, idMtr=$idMtr, `when`=$APP_araTimeSerial, `total`=$total, MtrControl='$MtrControl';";//20130502
//
//    $esOK = $APP_BdD->Execute($sql);
//    if(!$esOK) {
//        APP_fesLog("Error - code 04 in PBnew_MtrPay: $sql.");
//        echo "ko#04";
//        return;
//    }
//
//
//        echo "ok#$MtrControl";
//

?>
