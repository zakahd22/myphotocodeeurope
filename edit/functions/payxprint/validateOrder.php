<?php
include '../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

//America/New_York
//date_default_timezone_set("Europe/Madrid");
    
    date_default_timezone_set("America/New_York");
    $now = utils::get_datetime();

    $error = false;
    
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    if(isset($_POST['p'])){
        $preu = $_POST['p'];
    }
    if(isset($_POST['q'])){
        $quantity = $_POST['q'];
    }

    $sql = "
        SELECT Pay_print_order.idDongle as idDongle, booths.rand_string as rand_string, Pay_print_order.idOwner as idOwner, 
        rentals.name as owner_name, rentals.App_email as owner_mail, Pay_print_order.quantitat as quantitat,
        Pay_print_order.preu as preu, Pay_print_order.proposedDate as proposedDate, Pay_print_order.validatedDate as validatedDate
        FROM Pay_print_order 
        LEFT JOIN booths
        ON booths.id = Pay_print_order.idDongle
        LEFT JOIN rentals
        ON rentals.id = Pay_print_order.idOwner
        WHERE idOrder = {$id}
    ";

    $CLD_CON->OpenRs($sql);
    if($CLD_CON->FetchArray()){
        $idDongle       = $CLD_CON->GetArrayField("idDongle");
        $rand_string    = $CLD_CON->GetArrayField("rand_string");
        $idOwner        = $CLD_CON->GetArrayField("idOwner");
        $owner_name     = $CLD_CON->GetArrayField("owner_name");
        $owner_mail     = $CLD_CON->GetArrayField("owner_mail");
        $old_qty        = $CLD_CON->GetArrayField("quantitat");
        $old_price      = $CLD_CON->GetArrayField("preu");
        $proposedDate   = $CLD_CON->GetArrayField("proposedDate");
        $validatedDate  = $CLD_CON->GetArrayField("validatedDate");
    }
    
    if($validatedDate == NULL){
        if(isset($quantity) && ($quantity != $old_qty)){
            $update = "UPDATE Pay_print_order SET quantitat = {$quantity} WHERE idOrder = $id";
            if($CLD_CON->Execute($update) == 0){
                $error = 1;
            }
        }

        if(isset($preu) && ($preu != $old_price)){
            $update = "UPDATE Pay_print_order SET preu = {$preu} WHERE idOrder = $id";
            if($CLD_CON->Execute($update) == 0){
                $error = 2;
            }
        }

        $update = "UPDATE Pay_print_order SET validatedDate = '{$now}' WHERE idOrder = $id";
        if($CLD_CON->Execute($update) == 0){
            $error = 3;
        }

        $sql = "SELECT Pay_print_order.idDongle AS idDongle, Pay_print_dongle.saldo AS saldo
                FROM Pay_print_order
                LEFT JOIN Pay_print_dongle ON Pay_print_dongle.idDongle = Pay_print_order.idDongle
                WHERE Pay_print_order.idOrder = {$id}";

        $CLD_CON->OpenRs($sql);
        if($CLD_CON->FetchArray() > 0){
            $idDongle = $CLD_CON->GetArrayField("idDongle");
            $saldo = $CLD_CON->GetArrayField("saldo");
        }

        $new_saldo = $saldo+$quantity;

        $update = "UPDATE Pay_print_dongle SET saldo = ($new_saldo) WHERE Pay_print_dongle.idDongle = $idDongle";
        if($CLD_CON->Execute($update) == 0){
            $error = 4;
        }

        if(!$error){
            require_once(G_PATH . 'common/mail.php');
            
            $mail = new mail();
            
            $mail->addAdress($owner_mail, "Owner");
            $mail->setSubject("Order num {$id} validated");

            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/pxp_orderActivated.html");

            $mail->addTemplateField("{owner_name}", $owner_name);
            $mail->addTemplateField("{order_number}", $id);
            $mail->addTemplateField("{dongle_string}", $rand_string);

            if(isset($quantity) && ($quantity != $old_qty)){
                $mail->addTemplateField("{quantity}", $quantity);
            }
            else{
                $mail->addTemplateField("{quantity}", $old_qty);
            }

            $mail->addTemplateField("{proposed}", $proposedDate);
            $mail->addTemplateField("{validated}", $now);

            $mail->applyTempplateFields();
            if($mail->send()){
                $result = true;
                echo "OK";
            }
            else{
                 echo "ERROR sending email to owner!";
                 utils::log($mail->retMsg, G_PATH . "logMailer", "validateOrder");
            }
        }
        else{
            echo "ERROR $error";
        }
    }
    else{
        echo "OK";
    }