<?php

/*
 * Actualització de saldo de prints.
 * No hauriem de comprovar res del PB
 */
$APP_common_no_idb = true;

require("common.php");
if(!$APP_dongleOK) return;

//if(isset($_POST['sg'])){ $sg = $_POST['sg'];} else {$sg = "no";}

if(!$APP_sg){echo "Error sg"; APP_fesLog("Error - PBprint_check, sg is empty"); return;}
if(!$APP_tact){echo "Error tact"; APP_fesLog("Error - PBprint_check, tact is empty");  return;}

//no ens calen paràmetres

//signatura:
$signature = strtoupper(sha1($APP_dongle.$APP_tact.$APP_seccode));
if($signature != $APP_sg){
    APP_fesLog("Error - PBprint_check, sg error local: $signature url:$APP_sg");
    echo "Error - sg";
    return;
}

//cal comprovar el saldo
//SELECT `idDongle`, `startDate`, `minStock`, `quantitat`, `preu`, `saldo` FROM `Pay_print_dongle`
//SELECT `idOrder`, `idDongle`, `idOwner`, `quantitat`, `preu`, `proposedDate`, `validatedDate`, `reportedDate`, `commissionDate` 
//FROM `Pay_print_order` WHERE 1
//SELECT `idDongle`, `startDate`, `print` FROM `Pay_print_sessions` WHERE 1

$PB_common_prints = 0;
require("common/PBprint_common_checkNewOrder.php");
if($PB_common_error) {
    echo $PB_common_errorStr;
    return;
}

echo "ok#$PB_common_esPayPrint#$PB_common_saldo";

?>
