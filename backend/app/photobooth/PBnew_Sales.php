<?php

$APP_common_no_idb = true;
require("common.php");
if(!$APP_dongleOK) return;


//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar


////PENDENT d'implementar més control d'accès (signatura, etc.) Ara és important que estigui controlada l'execució
////ara simplement APP_base64_decode_custom
//if(isset($_REQUEST['t'])){ $tAct = $_REQUEST['t'];} else die("ko#3");
//if(isset($_REQUEST['c'])){ $control = APP_base64_decode_custom20($_REQUEST['c']);} else {die("ko#4");}
//if($tAct != $control){
//    die("ko#5");
//}

//https ://www.myphotocode.com/ app/photobooth/PBnew_Sales.php?pb=313&dongle=93289928&idmaq=GMEG&t=20201002124645&idb=7919&playCode=GMEGD6YT8F&idProducte=1062&material=MS&quantity=3&money=3

//SELECT `idSales`, `when`, `idBooth`, `idDongle`, `playCode`, `idProducte`, `descrProducte`, `material`, `quantity`, `money` FROM `App_sales` WHERE 1

if(isset($_REQUEST['t'])){
    
    if(!$_REQUEST['t']){ 
        $APP_inTimeSerial = $APP_araTimeSerial;
    }
    else{
        $APP_inTimeSerial = $APP_BdD->myDateTimeSerialFull($_REQUEST['t']);
    }
} 
else {
    $APP_inTimeSerial = $APP_araTimeSerial;
}


if(isset($_REQUEST['playCode'])){ $playCodeSql = ", playCode='{$_REQUEST['playCode']}'";} else {echo  "Error - code 01"; return;}

if(isset($_REQUEST['idProducte'])){ $idProducteSql = ", idProducte=".$_REQUEST['idProducte'];} else {$idProducteSql = "";}
if(isset($_REQUEST['descrProducte'])){ $descrProducteSql = ", descrProducte='{$_REQUEST['descrProducte']}'";} else {$descrProducteSql = "";}
if(isset($_REQUEST['material'])){ $materialSql = ", material='{$_REQUEST['material']}'";} else {$materialSql = "";}
if(isset($_REQUEST['quantity'])){ $quantitySql = ", `quantity`={$_REQUEST['quantity']}";} else {$quantitySql = "";}
if(isset($_REQUEST['money'])){ $moneySql = ", `money`={$_REQUEST['money']}";} else {$moneySql = "";}




$sql = "INSERT INTO `App_sales` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle";
$sql.=$playCodeSql;
$sql.=$idProducteSql;
$sql.=$descrProducteSql;
$sql.=$materialSql;
$sql.=$quantitySql;
$sql.=$moneySql;

$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    
    APP_fesLogDebbug("Error  $APP_BdD->errno,$APP_BdD->error   sql: $sql","logDebug202010sales");
    echo "Error - Sales - Database insert";
    return;

}


echo "OK#1";



?>
