<?php
require("common.php");


//20161025logPB INICI
//20161025logPB APP_fesLog("20161021PB_play idPB: $idb. REQUEST['idb']: " . $_REQUEST['idb']); //20161021vic
$PB2check = array(7617,6918,7178);
if(in_array($_REQUEST['idb'], $PB2check))
    APP_fesLog("20161025PB_play idPB: $idb. s: " . $_REQUEST['s'].". t: ". $_REQUEST['t']); 

//20161025logPB FINAL

//20140708 INICI
if ($APP_common_error) {
//20161021vic    APP_fesLog("Error20140708 in PB_Play.");    
    APP_fesLog("Error20140708-20161021 in PB_Play($idb): $APP_common_error"); //20161021vic
    return;
}
//20140708 FINAL

if (!$APP_dongleOK)
    return;

//llegir params i crear registre
//SELECT `idInfo`, `when`, `idBooth`, `idDongle`, `typeInfo`, `money`, `currency`, `stock`, `i1`, `i2`, `i3`, `str1`, `str2` FROM `App_info` WHERE 1
//typeInfo serà 10
if (isset($_REQUEST['m'])) {
    $money = $_REQUEST['m'];
}
if (isset($_REQUEST['m2'])) {
    $money2 = $_REQUEST['m2'];
} //20131029money (extracopies)

if (isset($_REQUEST['c'])) {
    $currency = $_REQUEST['c'];
}
if (isset($_REQUEST['s'])) {
    $stock = $_REQUEST['s'];
}
if (isset($_REQUEST['i1'])) {
    $i1 = $_REQUEST['i1'];
}//id de producte
if (isset($_REQUEST['i2'])) {
    $i2 = $_REQUEST['i2'];
}//20131029??? accumulat de coins entre reports

if (isset($_REQUEST['i3'])) {
    $i3sql = ", i3=" . $_REQUEST['i3'];
} else {
    $i3sql = "";
}//20131029money PB_infoMoneyCoins
if (isset($_REQUEST['i4'])) {
    $i4sql = ", i4=" . $_REQUEST['i4'];
} else {
    $i4sql = "";
}//20131029money PB_infoMoneyCard
if (isset($_REQUEST['i5'])) {
    $i5sql = ", i5=" . $_REQUEST['i5'];
} else {
    $i5sql = "";
}//20131029money PB_infoMoneyInet

if (isset($_REQUEST['str1'])) {
    $str1 = $_REQUEST['str1'];
}//20131029money DCrelease

$sql = "INSERT INTO  `App_info` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=10";
if ($money)
    $sql.=",money=$money";
//20140411merda!!     if($money2) $sql.=",money2=$money";//20131029money (extracopies)
if ($money2)
    $sql.=",money2=$money2"; //20140411merda!!     //20131029money (extracopies)
if ($currency)
    $sql.=",currency='$currency'";
if ($stock)
    $sql.=",stock=$stock";
if ($i1)
    $sql.=",i1=$i1";
if ($i2)
    $sql.=",i2=$i2"; //20131029???
$sql.=$i3sql; //20131029money PB_infoMoneyCoins
$sql.=$i4sql; //20131029money PB_infoMoneyCard
$sql.=$i5sql; //20131029money PB_infoMoneyInet
if ($str1)
    $sql.=",str1='$str1'"; //20131029money DCrelease



$sql.=",db_time=$APP_araTimeSerial";//20170627dbtimeExpr


$esOK = $APP_BdD->Execute($sql);
if (!$esOK) {
    echo "Error - Play - Database insert: $sql.";

    APP_fesLog("20161021PB_play idPB: $idb. Error - Play - Database insert: $sql."); //20161021vic

    return;
}


echo $APP_okResp; //20140713
//control d'alerta money
//20131029???if(isset($_REQUEST['i2'])){ 
//20131029???    $APP_common_money = $_REQUEST['i2'];
//20131029??? no entenc per què estava això    $APP_common_money = $money;//20130524-01

if (isset($i2)) {
    $APP_common_money = $i2;
//20131029??? no entenc per què estava això    $APP_common_money = $money;//20130524-01

    ob_start(); //20140713
    include 'common/APP_common_alertMoney.php';
    if ($APP_common_error)
        return;

//20140713 INICI
    $ret = ob_get_contents();
    ob_end_clean();
    if (strlen($ret) > 0) {
        APP_fesLog("Error 20140713 PB_Play - APP_common_alertMoney: $ret");
    }
//20140713 FINAL
}
//control d'alerta stock
$APP_common_stock = $stock;

ob_start(); //20140713
include 'common/APP_common_alertStock.php';
//20140713 INICI
$ret = ob_get_contents();
ob_end_clean();
if (strlen($ret) > 0) {
    APP_fesLog("Error 20140713 PB_Play - APP_common_alertStock: $ret");
}
//20140713 FINAL

if ($APP_common_error)
    return;



//20130524 INICI continuem amb send messages de APNS després de contestar (aqui sempre s'ha insertat un missatge
//20130524 echo $APP_okResp;

//20170220apns
//if ($APNS_MessageAdded) {
//    ignore_user_abort(true);
//    header("Connection: close");
//    header("Content-Length: " . mb_strlen($APP_okResp));
////20140713        echo $APP_okResp;
//    flush();
//    APNS_sendMessages();
//} else {
////20140713        echo $APP_okResp;
//}
//20130524 FINAL 
?>
