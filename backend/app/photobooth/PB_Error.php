<?php
$APP_common_alertErrorKO = true;//20130523

//20140708 INICI
if($APP_common_error){
    APP_fesLog("Error20140708 in PB_Error.");
    return;
}
//20140708 FINAL
require("common.php");


if(!$APP_dongleOK) return;


//llegir params i crear registre
//SELECT `idInfo`, `when`, `idBooth`, `idDongle`, `typeInfo`, `money`, `currency`, `stock`, `i1`, `i2`, `i3`, `str1`, `str2` FROM `App_info` WHERE 1
//typeInfo serà 40
//20131029no cal if(isset($_REQUEST['m'])){ $money = $_REQUEST['m'];}
//20131029no calif(isset($_REQUEST['c'])){ $currency = $_REQUEST['c'];}
//20131029no calif(isset($_REQUEST['s'])){ $stock = $_REQUEST['s'];}
if(isset($_REQUEST['i1'])){ $i1 = $_REQUEST['i1'];}//tipus error


//20130523 INICI
if(!$i1) {
    echo "Error - type not found - code 01.1 ";
    return;

}

//els errors entren a la gestió d'alertes, cal comprovar si el PB ja en té una del mateix tipus sense resoldre
// l'alerta d'erros es resoldrà quan rebem info de funcionament d'un photobooth (NOTA: creiem que serà aixì)
    $APP_common_alertError = "5$i1";
    
//    $APP_common_alertErrorKO = true;
    include 'common/APP_common_alertError.php';
    if($APP_common_error) return;


//20130523 FINAL


    if($APP_common_errorToInfo){//20130523

        $sql = "INSERT INTO  `App_info` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle,`typeInfo`=40";
//20131029no cal        if($money) $sql.=",money=$money";
//20131029no cal        if($currency) $sql.=",currency=$currency";
//20131029no cal        if($stock) $sql.=",stock=$stock";
        $sql.=",i1=$i1";
        

$sql.=",db_time=$APP_araTimeSerial";//20170627dbtimeExpr


        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK) {
            echo "Error - Error - Database insert: $sql.";
            return;

        }

    }//20130523

//20130523 INICI ja es fa a APP_common_alertError
//posem l'estat del booth en error, quan arribi un play ja l¡esborrarà (si cal)
//$sql = "UPDATE App_booths SET estat=1 WHERE idBooth = $APP_idBooth";
//$esOK = $APP_BdD->Execute($sql);
//if(!$esOK) {
//    echo "Error - Error - code 01 $sql.";
//    $APP_common_error = true;
//    return;
//
//}
//20130523 FINAL

    
    
    //20130524 INICI continuem amb send messages de APNS després de contestar (aqui sempre s'ha insertat un missatge
    //20130524 echo $APP_okResp;
    
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
