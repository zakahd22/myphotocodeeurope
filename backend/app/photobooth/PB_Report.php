<?php
require("common.php");

//20140708 INICI
if($APP_common_error){
    APP_fesLog("Error20140708 in PB_Report.");
    return;
}
//20140708 FINAL


if(!$APP_dongleOK) return;


//llegir params i crear registre
//SELECT `idInfo`, `when`, `idBooth`, `idDongle`, `typeInfo`, `money`, `currency`, `stock`, `i1`, `i2`, `i3`, `str1`, `str2` FROM `App_info` WHERE 1
//typeInfo serà 20
//20131029rep if(isset($_REQUEST['m'])){ $money = $_REQUEST['m'];}
if(isset($_REQUEST['c'])){ $currency = $_REQUEST['c'];}
if(isset($_REQUEST['s'])){ $stock = $_REQUEST['s'];}
if(isset($_REQUEST['i1'])){ $i1 = $_REQUEST['i1'];}//report number
if(isset($_REQUEST['i2'])){ $i2 = $_REQUEST['i2'];}//plays
if(isset($_REQUEST['i3'])){ $i3 = $_REQUEST['i3'];}//coins
if(isset($_REQUEST['str1'])){ $str1 = $_REQUEST['str1'];}//versió s/w




$sql = "INSERT INTO  `App_info` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=20";
//20131029rep if($money) $sql.=",money=$money";
if($currency) $sql.=",currency='$currency'";
if($stock) $sql.=",stock=$stock";
if($i1) $sql.=",i1=$i1";
if($i2) $sql.=",i2=$i2";
if($i3) $sql.=",i3=$i3";
if($str1) $sql.=",str1='$str1'";//versió

$sql.=",db_time=$APP_araTimeSerial";//20170627dbtimeExpr


$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    echo "Error - Report - Database insert: $sql.";
    return;

}

//esborrar possible alerta money
$sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = 12;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    echo "Error - Report - code 01 $sql.";
    $APP_common_error = true;
    return;

}
//cal actualitzar l'estat del booth
include 'common/APP_common_checkAlerts.php';
if($APP_common_error) return;



//control d'alerta stock
$APP_common_stock = $stock;

include 'common/APP_common_alertStock.php';
if($APP_common_error) return;


echo $APP_okResp;


//enviarem el report
//20150628 $APP_reportNumber = $i1; //20150626
//20150703  if($i1) $APP_reportNumber = "#$i1"; //20150628
if($i1) $APP_reportNumber = "$i1"; //20150703  

include_once ('../owner/Repdc_common.php');
include('../owner/Repdc_collection.php');


?>
