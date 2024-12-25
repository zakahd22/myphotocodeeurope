<?php
$IsPDnew = 1;

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
if(isset($_REQUEST['i4'])){ $i4 = $_REQUEST['i4'];}//202103virtualMoney
if(isset($_REQUEST['str1'])){ $str1 = $_REQUEST['str1'];}//versió s/w




$sql = "INSERT INTO  `App_info` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=20";
//20131029rep if($money) $sql.=",money=$money";
if($currency) $sql.=",currency='$currency'";
if($stock) $sql.=",stock=$stock";
if($i1) $sql.=",i1=$i1";
if($i2) $sql.=",i2=$i2";
if($i3) $sql.=",i3=$i3";
if($i4) $sql.=",i4=$i4";//202103virtualMoney
if($str1) $sql.=",str1='$str1'";//versió

if($APP_tactSql) $sql.=",pbs_time=$APP_tactSql "; //20170627tact
$sql.=",db_time=$APP_araTimeSerial";//20170627dbtime

//20170629pb  $sql.=",PBnew=1";
    if(isset($APP_pbSql)){$sql.=$APP_pbSql;} else{$sql.=",PBnew=1";}//20170629pb  


$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    if($APP_BdD->errno != 1062){//202201dup
    echo "Error - Report - Database insert: $sql.";
    APP_fesLogDebbug("Error 20170629-202201 $APP_BdD->errno,$APP_BdD->error   sql: $sql","logDebug20170629-202201pb");
    return;
    }//202201dup

}

APP_fesLogDebbug("PBnewReport TRACE INICI *******************","logDebug20170220");


//esborrar possible alerta money
$sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = 12;";

APP_fesLogDebbug("PBnewReport TRACE01 sql: $sql","logDebug20170220");

$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    echo "Error - Report - code 01 $sql.";
    $APP_common_error = true;
    return;

}


APP_fesLogDebbug("PBnewReport TRACE02 before APP_common_checkAlerts","logDebug20170220");


//cal actualitzar l'estat del booth
include 'common/APP_common_checkAlerts.php';
if($APP_common_error) return;



//control d'alerta stock
$APP_common_stock = $stock;

APP_fesLogDebbug("PBnewReport TRACE02 before APP_common_alertStock","logDebug20170220");


include 'common/APP_common_alertStock.php';
if($APP_common_error) return;


echo $APP_okResp;


//enviarem el report
//20150628 $APP_reportNumber = $i1; //20150626
//20150703  if($i1) $APP_reportNumber = "#$i1"; //20150628
if($i1) $APP_reportNumber = "$i1"; //20150703  

APP_fesLogDebbug("PBnewReport TRACE02 before Repdc_common","logDebug20170220");


include_once ('../owner/Repdc_common.php');

APP_fesLogDebbug("PBnewReport TRACE02 before Repdc_collection","logDebug20170220");

include('../owner/Repdc_collection.php');



APP_fesLogDebbug("PBnewReport TRACE FINAL *******************","logDebug20170220");


?>
