<?php
require("../common/APP_BdD.php");

//TRACE
//var_dump($_REQUEST);

//info des dels PB;
if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];} else $idBooth = ""; //caldrà trobar el PB segons dongle
if(isset($_REQUEST['c'])){ $code = $_REQUEST['c'];} else $code = ""; //caldrà trobar el dongle segons PB

//ara és una simulació

if($idBooth == "" && $code == "") {echo "KO-1"; return;}

$idDongle =1;//ara és una simulació, caldrà comprovar que el PB existeix i llegir idDongle


if(isset($_REQUEST['t'])){ $typeInfo = $_REQUEST['t'];} else return;

if(isset($_REQUEST['m'])){ $money = $_REQUEST['m'];}
if(isset($_REQUEST['c'])){ $currency = $_REQUEST['c'];}
if(isset($_REQUEST['s'])){ $stock = $_REQUEST['s'];}
if(isset($_REQUEST['i1'])){ $i1 = $_REQUEST['i1'];}
if(isset($_REQUEST['i2'])){ $i2 = $_REQUEST['i2'];}
if(isset($_REQUEST['i3'])){ $i3 = $_REQUEST['i3'];}
if(isset($_REQUEST['str1'])){ $str1 = $_REQUEST['$str1'];}
if(isset($_REQUEST['str2'])){ $str2 = $_REQUEST['$str2'];}

$sql = "INSERT INTO  `App_info` (`when`,`idBooth`,`idDongle`,`typeInfo`";
if($money) $sql.=",money";
if($currency) $sql.=",currency";
if($stock) $sql.=",stock";
if($i1) $sql.=",i1";
if($i2) $sql.=",i2";
if($i3) $sql.=",i3";
if($str1) $sql.=",str1";
if($str2) $sql.=",str2";

$ara = new DateTime("now");

$sql.=") VALUES ({$APP_BdD->myDateTimeSerial($ara)},$idBooth,$idDongle,$typeInfo";
if($money) $sql.=",$money";
if($currency) $sql.=",$currency";
if($stock) $sql.=",$stock";
if($i1) $sql.=",$i1";
if($i2) $sql.=",$i2";
if($i3) $sql.=",$i3";
if($str1) $sql.=",'$str1'";
if($str2) $sql.=",'$str2'";

$sql.=");";
//echo "<br/>TRACE $sql";//TRACE
$esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO-2";  }


?>
