<?php
$IsPDnew = 1;
$isPBnew_Play = 1;
require("common.php");

////20161025logPB INICI
////20161025logPB APP_fesLog("20161021PB_play idPB: $idb. REQUEST['idb']: " . $_REQUEST['idb']); //20161021vic
//$PB2check = array(7617,6918,7178);
//if(in_array($_REQUEST['idb'], $PB2check))
//    APP_fesLog("20161025PBnew_play idPB: $idb. s: " . $_REQUEST['s'].". t: ". $_REQUEST['t']); 
//
////20161025logPB FINAL

//20140708 INICI
if($APP_common_error){
//20161107    APP_fesLog("Error20140708 in PB_Play.");
    APP_fesLog("Error20140708-20161107 in PBnew_Play($idb): $APP_common_error.");//20161107
    return;
}
//20140708 FINAL

if(!$APP_dongleOK) return;


//llegir params i crear registre
//SELECT `idInfo`, `when`, `idBooth`, `idDongle`, `typeInfo`, `money`, `currency`, `stock`, `i1`, `i2`, `i3`, `str1`, `str2` FROM `App_info` WHERE 1
//typeInfo serà 10
if(isset($_REQUEST['m'])){ $money = $_REQUEST['m'];}
if(isset($_REQUEST['m2'])){ $money2 = $_REQUEST['m2'];} //20131029money (extracopies)

if(isset($_REQUEST['c'])){ $currency = $_REQUEST['c'];}
if(isset($_REQUEST['s'])){ $stock = $_REQUEST['s'];}
if(isset($_REQUEST['i1'])){ $i1 = $_REQUEST['i1'];}//id de producte
if(isset($_REQUEST['i2'])){ $i2 = $_REQUEST['i2'];}//20131029??? accumulat de coins entre reports

if(isset($_REQUEST['i3'])){ $i3sql = ", i3=".$_REQUEST['i3'];} else {$i3sql = "";}//20131029money PB_infoMoneyCoins
if(isset($_REQUEST['i4'])){ $i4sql = ", i4=".$_REQUEST['i4'];} else {$i4sql = "";}//20131029money PB_infoMoneyCard
if(isset($_REQUEST['i5'])){ $i5sql = ", i5=".$_REQUEST['i5'];} else {$i5sql = "";}//20131029money PB_infoMoneyInet

if(isset($_REQUEST['i6'])){ $i6sql = ", i6=".$_REQUEST['i6'];} else {$i6sql = "";}//20200505virtualMoney


if(isset($_REQUEST['str1'])){ $str1 = $_REQUEST['str1'];}//20131029money DCrelease
if(isset($_REQUEST['str2'])){ $str2 = $_REQUEST['str2'];}//20170116playCode


//201502 INICI
if(isset($_REQUEST['in1'])){ $in1sql = ", in1=".$_REQUEST['in1'];} else {$in1sql = "";}
if(isset($_REQUEST['in2'])){ $in2sql = ", in2=".$_REQUEST['in2'];} else {$in2sql = "";}
if(isset($_REQUEST['in3'])){ $in3sql = ", in3=".$_REQUEST['in3'];} else {$in3sql = "";}
if(isset($_REQUEST['in4'])){ $in4sql = ", in4=".$_REQUEST['in4'];} else {$in4sql = "";}
if(isset($_REQUEST['in5'])){ $in5sql = ", in5=".$_REQUEST['in5'];} else {$in5sql = "";}
if(isset($_REQUEST['in6'])){ $in6sql = ", in6=".$_REQUEST['in6'];} else {$in6sql = "";}
if(isset($_REQUEST['in7'])){ $in7sql = ", in7=".$_REQUEST['in7'];} else {$in7sql = "";}
//201502 FINAL
if(isset($_REQUEST['in8'])){ $in8sql = ", in8=".$_REQUEST['in8'];} else {$in8sql = "";}//20150512infoPrints



if(isset($_REQUEST['prop'])){ $propsql = ", prop=".$_REQUEST['prop'];} else {$propsql = "";}//20220316prop



$sql = "INSERT INTO  `App_info` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=10";
if($money) $sql.=",money=$money";
//20140411merda!!     if($money2) $sql.=",money2=$money";//20131029money (extracopies)
if($money2) $sql.=",money2=$money2";//20140411merda!!     //20131029money (extracopies)
if($currency) $sql.=",currency='$currency'";
if($stock) $sql.=",stock=$stock";
if($i1) $sql.=",i1=$i1";
if($i2) $sql.=",i2=$i2";//20131029???
$sql.=$i3sql;//20131029money PB_infoMoneyCoins
$sql.=$i4sql;//20131029money PB_infoMoneyCard
$sql.=$i5sql;//20131029money PB_infoMoneyInet

$sql.=$i6sql;//20200505virtualMoney

//201502 INICI
$sql.=$in1sql;
$sql.=$in2sql;
$sql.=$in3sql;
$sql.=$in4sql;
$sql.=$in5sql;
$sql.=$in6sql;
$sql.=$in7sql;
//201502 FINAL
$sql.=$in8sql;//20150512infoPrints

$sql.=$propsql;//20220316prop

if($str1) $sql.=",str1='$str1'";//20131029money DCrelease

//20170116playCode INICI
if($str2) $sql.=",str2='$str2'";

//20170627tact INICI
//if(isset($_REQUEST['tact'])){
//    $tact = $_REQUEST['tact'];
//    if(strlen($tact) == 14) $sql.=",pbs_time=" . $APP_BdD->myDateTimeSerialFull($_REQUEST['tact']);
////20170626tact INICI    
//    elseif(strlen($tact) > 14){
////        $tmp = \substr($tact, 0, 14);
////        $format = 'Y-m-d H:i:s';
//        $tmpTact = $APP_BdD->myDateTimeSerialFull(substr($tact, 0, 14));
//        try{
//           // $tmpdate = DateTime::createFromFormat('Y-m-d H:i:s', $tmpTact);
//            $tmpdate = new DateTime($tmpTact);
//            if(!$tmpdate){
//                APP_fesLogDebbug("Error 20170626-01 ($APP_idBooth,$tmpTact) tact: $tact","logDebug20170626tact");
//            }
//            else{
//                APP_fesLogDebbug("Error 20170626-ok ($APP_idBooth,$tmpTact) tact: $tact","logDebug20170626tact");
//                $sql.=",pbs_time=$tmpdate ";
//            }
//        }
//        catch (Exception $e) {
//            //res
//            APP_fesLogDebbug("Error 20170626-02 ($APP_idBooth,$tmpTact) tact: $tact","logDebug20170626tact");
//        }
//        
//    }
//    else APP_fesLogDebbug("Error 20170626-03 ($APP_idBooth,$tmpTact) tact: $tact","logDebug20170626tact");
////20170626tact FINAL    
//} 

if($APP_tactSql) $sql.=",pbs_time=$APP_tactSql ";
//20170627tact FINAL

$sql.=",db_time=$APP_araTimeSerial";


//20170116playCode FINAL

//20170629pb  $sql.=",PBnew=1";
    if(isset($APP_pbSql)){$sql.=$APP_pbSql;} else{$sql.=",PBnew=1";}//20170629pb  


//APP_fesLogDebbug("TRACE 20170627act APP_tactSql: $APP_tactSql,  sql: $sql","logDebug20170627tact");

APP_fesLogDebbug("TRACE 20170629 sql: $sql","logDebug20170629pb");

$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    if($APP_BdD->errno != 1062){//202201dup
    
    APP_fesLogDebbug("Error 20170629-202201 $APP_BdD->errno,$APP_BdD->error   sql: $sql","logDebug20170629-202201pb");
    echo "Error - Play - Database insert: $sql.";
    return;
    }//202201dup

}


echo $APP_okResp;//20140713

//control d'alerta money
//20131029???if(isset($_REQUEST['i2'])){ 
//20131029???    $APP_common_money = $_REQUEST['i2'];
//20131029??? no entenc per què estava això    $APP_common_money = $money;//20130524-01
    
if(isset($i2)){ 
    $APP_common_money = $i2;
//20131029??? no entenc per què estava això    $APP_common_money = $money;//20130524-01
    
    ob_start();//20140713
    include 'common/APP_common_alertMoney.php';
    if($APP_common_error) return;
    
//20140713 INICI
$ret = ob_get_contents();
ob_end_clean();
if(strlen($ret)>0){
    APP_fesLog("Error 20140713 PB_Play - APP_common_alertMoney: $ret");
}
//20140713 FINAL

} 
//control d'alerta stock
$APP_common_stock = $stock;

ob_start();//20140713
include 'common/APP_common_alertStock.php';
//20140713 INICI
$ret = ob_get_contents();
ob_end_clean();
if(strlen($ret)>0){
    APP_fesLog("Error 20140713 PB_Play - APP_common_alertStock: $ret");
}
//20140713 FINAL

if($APP_common_error) return;



    //20130524 INICI continuem amb send messages de APNS després de contestar (aqui sempre s'ha insertat un missatge
    //20130524 echo $APP_okResp;

//20170220apns
//    if($APNS_MessageAdded){
//        ignore_user_abort(true);
//        header("Connection: close");
//        header("Content-Length: " . mb_strlen($APP_okResp));
////20140713        echo $APP_okResp;
//        flush();    
//        APNS_sendMessages();
//    }
//    else{
////20140713        echo $APP_okResp;
//    }
    //20130524 FINAL 


?>
