<?php

//control i common de PBnew
$APP_common_no_idb = true;
$APP_common_mevaBdD = true;
require("common.php");
if(!$APP_dongleOK) return;


//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar



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
//APP_fesLogDebbug("TRACEdebug  tAct: $tAct","logUsbStockConfirm");

if(isset($_REQUEST['i'])){ $idUsbStock =  APP_base64_decode_custom20($_REQUEST['i']);} else {die("ko#5");}
if(isset($_REQUEST['d'])){ $codeUsbStock =  APP_base64_decode_custom20($_REQUEST['d']);} else {die("ko#6");}

//APP_fesLogDebbug("TRACEdebug id: $idUsbStock from {$_REQUEST['i']} .   codeUsbStock: $codeUsbStock from {$_REQUEST['d']}","logUsbStockConfirm");

/* @var $APP_BdD BdD */

$codeUsbStockIsOk = false;
//20201022ja $sql = "SELECT confirmUsbStock FROM App_usbStock WHERE idUsbStock=$idUsbStock AND  codeUsbStock='$codeUsbStock';"; 

$sql = "SELECT confirmUsbStock,`confirmed` FROM App_usbStock WHERE idUsbStock=$idUsbStock AND  codeUsbStock='$codeUsbStock';"; //20201022ja 
$infoKo = "Not found\t";//20201022ja
//APP_fesLogDebbug("TRACEdebug  sql: $sql","logUsbStockConfirm");

$esOK = $APP_BdD->OpenRs($sql);
if($esOK){
  if($APP_BdD->FetchRs()){
      $confirmUsbStock =  $APP_BdD->GetField(1);
//      APP_fesLogDebbug("TRACEdebug  confirmUsbStock: $confirmUsbStock","logUsbStockConfirm");
//20201022ja INICI
//20201022ja      $codeUsbStockIsOk = true;
      $dataConfirmat = $APP_BdD->GetFieldDateTime(2);
//      APP_fesLogDebbug("TRACEdebug  dataConfirmat: $dataConfirmat","logUsbStockConfirm");
      if($dataConfirmat){
          $infoKo = "Already confirmed\t".$dataConfirmat->format("Y-m-d H:i:s");
//      APP_fesLogDebbug("TRACEdebug  infoKo: $infoKo","logUsbStockConfirm");
      }
      else{
        $codeUsbStockIsOk = true;
          
      }
//20201022ja FINAL
  }
  $APP_BdD->CloseRs();
}
else{
//    APP_fesLogDebbug("ERROR on sql '$sql' ","logUsbStockConfirm");
    die("ko#7");
}
if(!$codeUsbStockIsOk){
    APP_fesLogDebbug("ERROR - !codeUsbStockIsOk \t$idUsbStock\t$codeUsbStock\t$APP_idBooth\t$APP_idDongle\t$infoKo","logUsbStockConfirm");
    die("ko#8");
}

//NOTA: no ho faréen dues crides, tot aqui
$sql = "UPDATE  App_usbStock SET `confirmed`=$APP_araTimeSerial, idBooth=$APP_idBooth, idDongle=$APP_idDongle ";
$sql .= " WHERE idUsbStock=$idUsbStock AND codeUsbStock = '$codeUsbStock';";
//APP_fesLogDebbug("TRACEdebug  sql: $sql","logUsbStockConfirm");
$nRegs = $APP_BdD->ExecuteAffected($sql);
if($nRegs <= 0) {
//    APP_fesLogDebbug("ERROR on sql '$sql' mysql_affected_rows: $nRegs","logUsbStockConfirm");
    die("ko#9");
}


echo "ok#". APP_base64_encode_custom21($confirmUsbStock);


//tot correcte



?>
