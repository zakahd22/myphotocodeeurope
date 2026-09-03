<?php

//error_reporting(0);
//ini_set('display_errors', 0);

error_reporting(E_ALL);//a eliminar
ini_set('display_errors', 1);//a eliminar

date_default_timezone_set(@date_default_timezone_get());

require("../common/APP_BdD_bk.php");
/* @var $APP_BdD BdD */

require("../common/APP_common.php");

if(isset($_REQUEST['f'])){ $myFolder = APP_base64_decode_custom20($_REQUEST['f']);} else die("ko#1");

if($myFolder != "DCusbStockPB"){//comprovació hard coded
    APP_fesLogDebbug("Wrong param f -  {$_REQUEST['f']} $myFolder","logUsbStockInit");
    die("ko#2");
}

//podem seguir

//SELECT `idUsbStock`, when, `codeUsbStock`, `confirmUsbStock`, `quantity`, `created`, `confirmed` FROM `App_usbStock`
//SELECT `idUsbStock`, `material`, `quantity` FROM `App_usbStockMaterial`

//PENDENT d'implementar més control d'accès (signatura, etc.) Ara és important que estigui controlada l'execució
//ara simplement APP_base64_decode_custom
if(isset($_REQUEST['t'])){ $tAct = $_REQUEST['t'];} else die("ko#3");
if(isset($_REQUEST['c'])){ $control = APP_base64_decode_custom20($_REQUEST['c']);} else die("ko#4");
if($tAct != $control){
    die("ko#5");
}
//$tAct és aaaammddhhmmss, anirà a when

//crearem un registre App_usbStock amb nou codis codeUsbStock`;  `confirmUsbStock el deixem per a quan confirmi l'escriptura a l'usb UsbStockInitDone
//guardarem quantity
//després un nou regeistre per a cada material

//paràmetres
if(isset($_REQUEST['q'])){ $stockQty = intval(APP_base64_decode_custom20($_REQUEST['q']));} else die("ko#6");

if(!$stockQty){
    APP_fesLogDebbug("Wrong param q - {$_REQUEST['q']} " . APP_base64_decode_custom20($_REQUEST['q']),"logUsbStockInit");
    die("ko#6");
}

//per als materials
if(isset($_REQUEST['m'])){ $materials = APP_base64_decode_custom20($_REQUEST['m']);} else die("ko#7");
$arr_materials = explode("|", $materials); //seran parelles de <material>|<quantitat>
$l = count($arr_materials);

APP_fesLogDebbug("TRACE - materials: $materials ","logUsbStockInit");

$ara = new DateTime("now");
$createdSql = $APP_BdD->myDateTimeSerial($ara);

$whenSql = $APP_BdD->myDateTimeSerialFull($tAct);

//codeUsbStock
$codeUsbStockIsOk = false;
for($i = 0; $i < 5; $i++){
    
    $codeUsbStock = rndm32(4);
    $sql = "SELECT * FROM App_usbStock WHERE codeUsbStock='$codeUsbStock';"; 
    $esOK = $APP_BdD->OpenRs($sql);
    if($esOK){
      if(!$APP_BdD->FetchRs()){
          $codeUsbStockIsOk = true;
      }
      $APP_BdD->CloseRs();
    }
    else{
        APP_fesLogDebbug("ERROR - ko query $sql: " . $APP_BdD->error. " - " . $APP_BdD->errno,"logUsbStockInit");
    }
    if($codeUsbStockIsOk) break;
}
if(!$codeUsbStockIsOk){
    APP_fesLogDebbug("ERROR - !codeUsbStockIsOk ","logUsbStockInit");
    die("ko#8");
}

//SELECT `idUsbStock`, when, `codeUsbStock`, `confirmUsbStock`, `quantity`, `created`, `confirmed` FROM `App_usbStock`
$sql = "INSERT INTO App_usbStock SET `when` = $whenSql, `codeUsbStock`='$codeUsbStock', `quantity`=$stockQty, created=$createdSql ;";
$idUsbStock = $APP_BdD->ExecuteInsert($sql);
if(!$idUsbStock) {
    APP_fesLogDebbug("ERRORon INSERT $sql " . $APP_BdD->error. " - " . $APP_BdD->errno,"logUsbStockInit");
    die("ko#9");
}

//ja tenim el registre

for($i=0;$i<$l;$i++){
    if($i >= $l) break;
    $codiMaterial = $arr_materials[$i];
    $i++;
    if($i >= $l) break;
    $quantity = intval($arr_materials[$i]);
    
    //SELECT `idUsbStock`, `material`, `quantity` FROM `App_usbStockMaterial`
    $sql = "INSERT INTO App_usbStockMaterial SET idUsbStock = $idUsbStock, `material`='$codiMaterial', `quantity`=$quantity ;";
    $esOK = $APP_BdD->Execute($sql); 
    if(!$esOK){ 
        APP_fesLogDebbug("ERRORon INSERT material $sql ","logUsbStockInit");
        die("ko#10");
    }

}
//tot correcte

echo "ok#" . APP_base64_encode_custom21($idUsbStock) . "#" . APP_base64_encode_custom21($codeUsbStock);


?>
