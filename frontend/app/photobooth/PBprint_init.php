<?php

/*
 * Acceptació des del PB amb el dongle de funcionament PayPrint
 * 
 * Resposta: ok#boolean#versio#saldo
    ·	boolean: 0: no és pay_per_print; 1: si que ho és
    ·	versió: versió a guardar al dongle (ara sempre 1)
    ·	saldo: saldo inicial

 */
$APP_common_no_idb = true;

require("common.php");
if(!$APP_dongleOK) return;


$myScript = "PBprint_init";

//20160119acceptem sense   if(!$APP_sg){echo "Error sg"; APP_fesLog("Error - $myScript, sg is empty"); return;}
if(!$APP_tact){echo "Error tact"; APP_fesLog("Error - $myScript, tact is empty");  return;}

//no ens calen paràmetres

if($APP_sg){//20160119acceptem sense
    //signatura:
    $signature = strtoupper(sha1($APP_dongle.$APP_tact.$APP_seccode));
    if($signature != $APP_sg){
        APP_fesLog("Error - $myScript, sg error local: $signature url:$APP_sg");
        echo "Error - sg";
        return;
    }
}//20160119acceptem sense

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

if($PB_common_esPayPrint){
    $sql = "UPDATE `Pay_print_dongle` SET  `startDate`=$APP_araTimeSerial WHERE idDongle=$APP_idDongle ;";
    
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK){
        APP_fesLog("Error - $myScript, Error2: $sql.");
        echo "Error02";
        return;
    }
    //20160518Mail_pxp
    else{
        $PB_mail = null;

        $sql = "
            SELECT booths.rand_string, rentals.name, rentals.App_email
            FROM booths 
            LEFT JOIN rentals
            ON rentals.id = booths.`rental_id`
            WHERE booths.id = {$APP_idDongle}
        ";

        $APP_BdD->OpenRs($sql);
        if($APP_BdD->FetchRs()){
            $camp=1;
            $APP_rand_string = $APP_BdD->GetField($camp);$camp++;
            $APP_rental_name = $APP_BdD->GetField($camp);$camp++;
            $APP_rental_mail = $APP_BdD->GetField($camp);$camp++;

            require_once G_PATH . "common/mail.php";
            
            $PB_mail = new mail();

            $PB_mail->addAdress($APP_rental_mail, "Owner");
            $PB_mail->setSubject("Payxprint Dongle {$APP_rand_string} accepted");

            if($PB_mail->setTemplate(G_PATH . "common/resources/templates/html/en/pxp_dongleStart.html")){
                $PB_common_pxp_saldo = $saldo;
                $PB_common_pxp_minStock = $minStock;
                $PB_common_pxp_quantitat = $quantitat;
                $PB_common_pxp_preu = $preu;

                $PB_mail->addTemplateField("{owner_name}", $APP_rental_name);
                $PB_mail->addTemplateField("{dongle_string}", $APP_rand_string);
                $PB_mail->addTemplateField("{saldo}", $PB_common_pxp_saldo);
                $PB_mail->addTemplateField("{min_stock}", $PB_common_pxp_minStock);
                $PB_mail->addTemplateField("{quantity}", $PB_common_pxp_quantitat);
                $PB_mail->addTemplateField("{price}", $PB_common_pxp_preu);

                $PB_mail->applyTempplateFields();
                if(!$PB_mail->send()){
                    APP_fesLog("PBprint_init, ErrorMAIL04: {$PB_mail->retMsg}");
                }
            }
            else{
                APP_fesLog("PBprint_init, ErrorMAIL05: {$PB_mail->retMsg}", "logAPPDebug");
            }
        }
    }
    //20160518Mail_pxp fi
}

//ara sempre versió 1
echo "ok#$PB_common_esPayPrint#1#$PB_common_saldo";

?>
