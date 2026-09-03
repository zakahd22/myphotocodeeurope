<?php
$APP_startSession = true;




//20161107controlExtra2 INICI
//if($_REQUEST['idb'] == 7550){
////    APP_fesLog("ExtraLog20161107_2 in PBnew_Start. ".  var_export($_REQUEST, true));
//    $controlExtra2 = var_export($_REQUEST, true);
//    var_dump($_REQUEST);
//}
//20161107controlExtra2 FINAL

//20140708 INICI
//if($APP_common_error){
//    APP_fesLog("Error20140708 in PB_Start.");
//    return;
//}
//20140708 FINAL
$IsPDnew = 1;
require("common.php");



//20161107controlExtra2 INICI
//if(isset($controlExtra2)){
//    APP_fesLog("ExtraLog20161107_2 in PBnew_Start. $controlExtra2");
//}
//20161107controlExtra2 FINAL

//20161107 INICI
if($APP_common_error){
    APP_fesLog("Error20161107 in PBnew_Start($idb): $APP_common_error.");
    return;
}

//20161107 FINAL
//
//APP_fesLogDebbug("Debbug -- startConnection (REQUEST['t']): {$_REQUEST['t']}", 'logSession');
//APP_fesLogDebbug("Debbug -- lastConnection (REQUEST['lt']): {$_REQUEST['lt']}", 'logSession');
//
//20170620cash INICI

ob_start();//20181109vic

if(isset($_REQUEST['c'])){ $currency = $_REQUEST['c'];}
if(isset($_REQUEST['i2'])){ $i2 = $_REQUEST['i2'];}//accumulat de coins entre reports
if(isset($_REQUEST['i3'])){ $i3sql = ", i3=".$_REQUEST['i3'];} else {$i3sql = "";}
if(isset($_REQUEST['i4'])){ $i4sql = ", i4=".$_REQUEST['i4'];} else {$i4sql = "";}
if(isset($_REQUEST['str1'])){ $str1 = $_REQUEST['str1'];}//DCrelease
if(isset($_REQUEST['str2'])){ $str2 = $_REQUEST['str2'];}//UPGRADEid

//20170620cash FINAL



//201706start INICI
//insertarem dos registres a APP_info: type 50 (Start) i type 60 (Stop)
//Stop pot no crear-se si és una restauració de BOOT
//recuprem info de 

if(isset($_REQUEST['vr'])){ $ver = "'".$_REQUEST['vr']."'";} else {$ver = "NULL";}
$lastHiha = false;
//20170627lastAct if(isset($_REQUEST['lt'])){ 
if(isset($_REQUEST['nwlt'])){ //20170627lastAct
    $lastTimeSerial = $APP_BdD->myDateTimeSerialFull($_REQUEST['nwlt']);
    if($lastTimeSerial != "myDateSerialError") $lastHiha = true;
} 

if($lastHiha){
//insertem Stop
    $sql = "INSERT INTO  `App_info` SET `when`=$lastTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=60";
//20170627tact INICI
//    if(isset($_REQUEST['tact'])){
//        $tact = $_REQUEST['tact'];
//        if(strlen($tact) == 14) $sql.=",pbs_time=" . $APP_BdD->myDateTimeSerialFull($_REQUEST['tact']);
//        else APP_fesLogDebbug("Debbug -- error  tact: $tact ", 'logSession');
//    } 
    if($APP_tactSql) $sql.=",pbs_time=$APP_tactSql ";
//20170627tact FINAL
    
    //20181109 INCI
    if(strlen($APP_araTimeSerial) == 0){
        APP_fesLogDebbug("Debbug -- APP_araTimeSerial was empty ", 'logSession');
        $ara = new DateTime("now");
        $APP_araTimeSerial = $APP_BdD->myDateTimeSerial($ara);
    }
    
    //20181109 FINAL
    
    
    $sql.=",db_time=$APP_araTimeSerial";

//20170629pb    $sql.=",PBnew=1";
    if(isset($APP_pbSql)){$sql.=$APP_pbSql;} else{$sql.=",PBnew=1";}//20170629pb
    
    
//20170620cash INICI
if($currency) $sql.=",currency='$currency'";
if($i2) $sql.=",i2=$i2";
$sql.=$i3sql;
$sql.=$i4sql;
if($str1) $sql.=",str1='$str1'";
if($str2) $sql.=",str2='$str2'";
//20170620cash FINAL

    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        if($APP_BdD->errno != 1062){//202201dup
        //APP_fesLogDebbug("Debbug -- insert Stop ($sql): $APP_BdD->errno, $APP_BdD->error ", 'logSession');
        APP_fesLogDebbug("Error 20170629-202201 $APP_BdD->errno,$APP_BdD->error   sql: $sql","logDebug20170629-202201pb");
        }//202201dup

    }
}

//insertem Start
$sql = "INSERT INTO  `App_info` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=50";
$sql.=",str1=$ver";
//20170627tact INICI
//if(isset($_REQUEST['tact'])){
//    $tact = $_REQUEST['tact'];
//    if(strlen($tact) == 14) $sql.=",pbs_time=" . $APP_BdD->myDateTimeSerialFull($_REQUEST['tact']);
//    else APP_fesLogDebbug("Debbug -- error  tact: $tact ", 'logSession');
//} 
    if($APP_tactSql) $sql.=",pbs_time=$APP_tactSql ";
//20170627tact FINAL
$sql.=",db_time=$APP_araTimeSerial";

//20170629pb $sql.=",PBnew=1";
    if(isset($APP_pbSql)){$sql.=$APP_pbSql;} else{$sql.=",PBnew=1";}//20170629pb

$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    if($APP_BdD->errno != 1062){//202201dup
    APP_fesLogDebbug("Debbug -- insert Start ($sql): $APP_BdD->errno, $APP_BdD->error ", 'logSession');
    }//202201dup

}





//201706start FINAL


//20161107controlExtra INICI
//if($idb == 7550){
//    APP_fesLog("ExtraLog20161107 in PBnew_Start($idb). tact: ".$_REQUEST['tact']. "; sg: ".$_REQUEST['sg']."; APP_dongleOK: $APP_dongleOK");
//}
//20161107controlExtra FINAL

if(!$APP_dongleOK) return;

if(isset($_REQUEST['ccsn']))//
{
    
    $sql = "UPDATE App_booths SET cardReaderSN ='{$_REQUEST['ccsn']}' WHERE idBooth=$APP_idBooth;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        ob_end_clean();//20181109vic
        echo "Error - Database update: $sql.";
        return;

    }//SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_boothDongle`
    APP_fesLogDebbug("APP_ccsn: ".$_REQUEST['ccsn']." ******************* idBooth:$APP_idBooth","log20181109");
}



    //20130524 INICI continuem amb send messages de APNS després de contestar (aqui sempre s'ha insertat un missatge
    //20130524 echo $APP_okResp;

APP_fesLogDebbug("APP_okResp; $APP_okResp *******************","log20181109");

ob_end_clean();//20181109vic

echo $APP_okResp;//20170220apns
//20170220apns
//    if($APNS_MessageAdded){
//        ignore_user_abort(true);
//        header("Connection: close");
//        header("Content-Length: " . mb_strlen($APP_okResp));
//        echo $APP_okResp;
//        flush();    
//        APNS_sendMessages();
//    }
//    else{
//        echo $APP_okResp;
//    }
    //20130524 FINAL 


?>
