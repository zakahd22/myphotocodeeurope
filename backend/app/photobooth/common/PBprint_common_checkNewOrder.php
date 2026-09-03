<?php

/*
 *  decidir si és PayPrint, comprovar el saldo amb minStock i crear comanda si cal
 * 
 * Nota: en alguns casos (PBprint_send) ens arribarà  $PB_common_nPrints per a descomptar
 * 
 * retornarà $PB_common_esPayPrint i $PB_common_saldo
 */

    $PB_common_error = 0;
    $PB_common_errorStr = "";
    
    $PB_common_esPayPrint = 0;
    $PB_common_saldo = 0;
    //llegim el saldo
    $sql = "SELECT saldo, minStock, quantitat, preu, booths.rental_id 
            FROM Pay_print_dongle
            LEFT JOIN booths
            ON Pay_print_dongle.idDongle = booths.id
            WHERE Pay_print_dongle.idDongle = $APP_idDongle            
            ";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        APP_fesLog("Error - Common_checkNewOrder, Error01: $sql.");
        $PB_common_error = 1;
        $PB_common_errorStr = "Error01 Common_checkNewOrder";
        return;
    }
    if($APP_BdD->FetchRs()){
        $camp = 1;
        $saldo = $APP_BdD->GetField($camp);$camp++;
        $minStock = $APP_BdD->GetField($camp);$camp++;
        $quantitat = $APP_BdD->GetField($camp);$camp++;
        $preu = $APP_BdD->GetField($camp);$camp++;
        $owner = $APP_BdD->GetField($camp);$camp++;
        
        $PB_common_pxp_saldo = $saldo;
        $PB_common_pxp_minStock = $minStock;
        $PB_common_pxp_quantitat = $quantitat;
        $PB_common_pxp_preu = $preu;
        
        $PB_common_esPayPrint = 1;
    }
    $APP_BdD->CloseRs();
    
    if(!$PB_common_esPayPrint){
        return;
    }
    
    $PB_common_saldo = $saldo - $PB_common_prints;
    
    
    APP_fesLog("TRACE Common_checkNewOrder; PB_common_saldo: $PB_common_saldo; minStock: $minStock");
   
    
    if($PB_common_saldo < $minStock){
        $calCrearOrder = true;
        //mirar si ja existeix una comanda pendent de validar
        $sql = "SELECT idOrder FROM Pay_print_order WHERE idDongle=$APP_idDongle AND idOwner=$owner AND `validatedDate` IS NULL;";
        
            APP_fesLog("TRACE Common_checkNewOrder: $sql");
        
        
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
            APP_fesLog("Error - Common_checkNewOrder, Error02: $sql.");
            $PB_common_error = 2;
            $PB_common_errorStr = "Error02 Common_checkNewOrder";
            return;
        }
        if($APP_BdD->FetchRs()){
            $calCrearOrder = false;
        }
        $APP_BdD->CloseRs();
        if($calCrearOrder){
            $sql = "INSERT INTO `Pay_print_order` SET idDongle=$APP_idDongle, idOwner=$owner, CLD_Distributor=$APP_CLD_Distributor,
                `quantitat`=$quantitat,`preu`=$preu,`proposedDate`=$APP_araTimeSerial;";

            APP_fesLog("TRACE Common_checkNewOrder: $sql");

            $APP_idOrder = $APP_BdD->ExecuteInsert($sql);
            if(!$APP_idOrder) {
                APP_fesLog("Common_checkNewOrder, Error03: $sql");
                $PB_common_error = 3;
                $PB_common_errorStr = "Error03 Common_checkNewOrder";
                return;

            }
            //20160512Mail_pxp
            else{
                $PB_mail = null;
                
                $sql = "
                    SELECT booths.rand_string, rentals.name, rentals.App_email
                    FROM Pay_print_order 
                    LEFT JOIN booths
                    ON booths.id = Pay_print_order.idDongle
                    LEFT JOIN rentals
                    ON rentals.id = Pay_print_order.idOwner
                    WHERE idOrder = {$APP_idOrder}
                ";
                
//                APP_fesLogDebbug("TRACE Common_checkNewOrder MAIL: $sql", "logAPPDebug");

                $APP_BdD->OpenRs($sql);
                if($APP_BdD->FetchRs()){
                    $camp=1;
                    $APP_rand_string = $APP_BdD->GetField($camp);$camp++;
                    $APP_rental_name = $APP_BdD->GetField($camp);$camp++;
                    $APP_rental_mail = $APP_BdD->GetField($camp);$camp++;
                    
                    
                    require_once G_PATH . "common/mail.php";
                    
                    $ara = new DateTime("now");
                    

                    $PB_mail = new mail();

                    $PB_mail->addAdress($APP_rental_mail, "Owner");
                    $PB_mail->setSubject("Order num {$APP_idOrder} generated");

                    if($PB_mail->setTemplate(G_PATH . "common/resources/templates/html/en/pxp_newOrder.html")){
                        $PB_mail->addTemplateField("{owner_name}", $APP_rental_name);
                        $PB_mail->addTemplateField("{dongle_string}", $APP_rand_string);
                        $PB_mail->addTemplateField("{order_number}", $APP_idOrder);
                        $PB_mail->addTemplateField("{quantity}", $quantitat);
                        $PB_mail->addTemplateField("{proposed}", APP_myDateAndTime($ara));

                        $PB_mail->applyTempplateFields();
                        if(!$PB_mail->send()){
                            APP_fesLog("Common_checkNewOrder, ErrorMAIL04: {$PB_mail->retMsg}");
                        }
                    }
                    else{
                        APP_fesLog("Common_checkNewOrder, ErrorMAIL05: {$PB_mail->retMsg}", "logAPPDebug");
                    }
                }
            }
            //20160512Mail_pxp fi
        }
    }
    //actualitzem el saldo
    if($PB_common_saldo != $saldo){
        $sql="UPDATE Pay_print_dongle SET saldo=$PB_common_saldo WHERE idDongle=$APP_idDongle;";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK) {
            APP_fesLog("Common_checkNewOrder, Error04: $sql");
            $PB_common_error = 4;
            $PB_common_errorStr = "Error04 Common_checkNewOrder";
            return;
        }
    }

?>
