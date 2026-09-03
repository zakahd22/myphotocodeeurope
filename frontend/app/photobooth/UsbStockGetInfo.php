<?php

error_reporting(0);
ini_set('display_errors', 0);

error_reporting(E_ALL);//a eliminar
ini_set('display_errors', 1);//a eliminar

date_default_timezone_set(@date_default_timezone_get());

require("../common/APP_BdD_bk.php");
/* @var $APP_BdD BdD */

require("../common/APP_common.php");

if(isset($_REQUEST['f'])){ $myFolder = APP_base64_decode_custom20($_REQUEST['f']);} else {die("ko#1");}

if($myFolder != "DCusbStockPB"){//comprovació hard coded
    return "ko#2";
}

//podem seguir

//SELECT `idUsbStock`, `when`, `codeUsbStock`, `confirmUsbStock`, `quantity`, `created`, `confirmed` FROM `App_usbStock`
//SELECT `idUsbStock`, `material`, `quantity` FROM `App_usbStockMaterial`

//PENDENT d'implementar més control d'accès (signatura, etc.) Ara és important que estigui controlada l'execució
//ara simplement APP_base64_decode_custom
if(isset($_REQUEST['t'])){ $tAct = $_REQUEST['t'];} else die("ko#3");
if(isset($_REQUEST['c'])){ $control = APP_base64_decode_custom20($_REQUEST['c']);} else {die("ko#4");}
if($tAct != $control){
    die("ko#5");
}

APP_fesLogDebbug("TRACE _REQUEST['i']; {$_REQUEST['i']} _REQUEST['d']: {$_REQUEST['d']}  ","logUsbStockGetInfo");


if(isset($_REQUEST['i'])){ $idUsbStock =  APP_base64_decode_custom21($_REQUEST['i']);} else {die("ko#5");}
if(isset($_REQUEST['d'])){ $codeUsbStock =  APP_base64_decode_custom21($_REQUEST['d']);} else {die("ko#6");}

$hihaRegistre = false;
$sql = "SELECT  `when`, `confirmUsbStock`, `quantity`, `created`, `confirmed` FROM `App_usbStock` WHERE idUsbStock=$idUsbStock AND  codeUsbStock='$codeUsbStock';"; 


APP_fesLogDebbug("TRACE sql: $sql  ","logUsbStockGetInfo");

$esOK = $APP_BdD->OpenRs($sql);
APP_fesLogDebbug("TRACE OpenRs: $esOK  ","logUsbStockGetInfo");
if($esOK){
  if($APP_BdD->FetchRs()){
      $camp = 1;
APP_fesLogDebbug("TRACE before GetFieldDateTime($camp)  ","logUsbStockGetInfo");
      $tmp = $APP_BdD->GetFieldDateTime($camp++); 
      
      if($tmp) $when = APP_myDateAndTime($tmp); else $when="-";
APP_fesLogDebbug("TRACE when: $when  ","logUsbStockGetInfo");
      
      $confirmUsbStock =  $APP_BdD->GetField($camp++);
      $quantity =  $APP_BdD->GetField($camp++);
      $tmp = $APP_BdD->GetFieldDateTime($camp++); 
      if($tmp) $created = APP_myDateAndTime($tmp); else $created="-";
APP_fesLogDebbug("TRACE created: $created  ","logUsbStockGetInfo");
      $tmp = $APP_BdD->GetFieldDateTime($camp++); 
      if($tmp) $confirmed = APP_myDateAndTime($tmp); else $confirmed="-";
APP_fesLogDebbug("TRACE confirmed: $confirmed  ","logUsbStockGetInfo");
      
      $hihaRegistre = true;
  }
  $APP_BdD->CloseRs();
}


if(!$hihaRegistre){
    APP_fesLogDebbug("RES - !hihaRegistre ","logUsbStockGetInfo");
//    die("ko#8#Not found");
    die("ok#". APP_base64_encode_custom20(" Not found!!!"));
}

APP_fesLogDebbug("TRACE hihaRegistre: $hihaRegistre when: $when confirmed: $confirmed","logUsbStockGetInfo");

$resposta = "UsbStock info:\r\n";
$resposta .= "  idUsbStock     : $idUsbStock\r\n";
$resposta .= "  codeUsbStock   : $codeUsbStock\r\n";
$resposta .= "  when (local)   : $when\r\n";
$resposta .= "  confirmUsbStock: $confirmUsbStock\r\n";
$resposta .= "  quantity       : $quantity\r\n";
$resposta .= "  created (host) : $created\r\n";
$resposta .= "  confirmed      : $confirmed\r\n";

$resposta .= "App_usbStockMaterial:\r\n";
$sql = "SELECT `material`, `quantity` FROM `App_usbStockMaterial` WHERE idUsbStock=$idUsbStock  ORDER BY `idUsbStockMaterial`;"; 
$esOK = $APP_BdD->OpenRs($sql);
if($esOK){
  while($APP_BdD->FetchRs()){
      $camp = 1;
      $material =  $APP_BdD->GetField($camp++);
      $quantity =  $APP_BdD->GetField($camp++);
      $resposta .= "  $material: $quantity\r\n";

  }
  $APP_BdD->CloseRs();
}
echo "ok#". APP_base64_encode_custom20($resposta);


//tot correcte



?>
