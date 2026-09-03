<?php

/*
 * per a demanar un codi de seguretat, sense paràmetres específics
 */
$APP_script = "PBnew_renewSec";
$APP_common_no_idb = true;

require("common.php");

//20200330unicode INICI
APP_fesLog("TRACE 20200330unicode - $APP_script: " . var_export($_REQUEST, true));
//20200330unicode FINAL

if(!$APP_dongleOK) return;

//if(!$APP_sg){echo "Error sg"; APP_fesLog("Error - $APP_script, sg is empty"); return;}
if(!$APP_tact){echo "Error tact"; APP_fesLog("Error - $APP_script, tact is empty");  return;}

//no ens calen paràmetres

//signatura, només si hi ha $APP_sg
if($APP_sg){
$signature = strtoupper(sha1($APP_dongle.$APP_tact.$APP_seccode));
if($signature != $APP_sg){
    APP_fesLog("Error - $APP_script, sg error local: $signature url:$APP_sg");
    echo "Error - sg";
    return;
}
}
$pre = rndm32(5);
$codi = rndm32(10);
$pos = rndm32(5);

$sql="UPDATE `booths` SET seccode='$codi' WHERE id=$APP_idDongle;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    APP_fesLog("$APP_script, Error01: $sql");
    echo "Error01";
    return;
}

echo "ok#$pre$codi$pos";
?>
