<?php

error_reporting(0);
ini_set('display_errors', 0);

//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar

date_default_timezone_set(@date_default_timezone_get());

require("../common/APP_BdD_bk.php");
/* @var $APP_BdD BdD */

require("../common/APP_common.php");

if(isset($_REQUEST['f'])){ $myFolder = APP_base64_decode_custom20($_REQUEST['f']);} else {die("ko#1");}

if($myFolder != "DCusbStockPB"){//comprovació hard coded
    return "ko#2";
}

//podem seguir

//SELECT `idUsbStock`, when, `codeUsbStock`, `confirmUsbStock`, `quantity`, `created`, `confirmed` FROM `App_usbStock`
//SELECT `idUsbStock`, `material`, `quantity` FROM `App_usbStockMaterial`

//PENDENT d'implementar més control d'accès (signatura, etc.) Ara és important que estigui controlada l'execució
//ara simplement APP_base64_decode_custom
if(isset($_REQUEST['t'])){ $tAct = $_REQUEST['t'];} else die("ko#3");
if(isset($_REQUEST['c'])){ $control = APP_base64_decode_custom20($_REQUEST['c']);} else {die("ko#4");}
if($tAct != $control){
    die("ko#5");
}


if(isset($_REQUEST['i'])){ $idUsbStock =  APP_base64_decode_custom20($_REQUEST['i']);} else {die("ko#5");}
if(isset($_REQUEST['d'])){ $codeUsbStock =  APP_base64_decode_custom20($_REQUEST['d']);} else {die("ko#6");}

$confirmUsbStock = rndm32(4);


//$ara = new DateTime("now");
//$createdSql = $APP_BdD->myDateTimeSerial($ara);


//SELECT `idUsbStock`, when, `codeUsbStock`, `confirmUsbStock`, `quantity`, `created`, `confirmed` FROM `App_usbStock`
$sql = "UPDATE  App_usbStock SET `confirmUsbStock`='$confirmUsbStock' WHERE idUsbStock=$idUsbStock AND codeUsbStock = '$codeUsbStock';";
$nRegs = $APP_BdD->ExecuteAffected($sql);
if($nRegs <= 0) {
    APP_fesLogDebbug("ERROR on UPDATE $sql mysql_affected_rows: $nRegs","logUsbStockInitDone");
    die("ko#7");
}

//tot correcte

echo "ok#" . APP_base64_encode_custom21($idUsbStock);


?>
