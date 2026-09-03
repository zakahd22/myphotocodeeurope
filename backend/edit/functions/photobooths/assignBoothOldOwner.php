<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$CLD_CON2 = clone($CLD_CON);
$ownerID = $_POST['owner'];
$idBooth = $_POST['idBooth'];
$subDisID = $_POST['subDis'];
$date = date("Y-m-d H:i:s");
$contactEmail = "";
$t = false;

$CLD_CON->OpenRs("SELECT serialnumber , owner FROM App_booths WHERE idBooth=$idBooth");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
}


$CLD_CON->OpenRs("SELECT name , App_email ,username , password FROM rentals WHERE id=$ownerID");
if($CLD_CON->FetchArray()){
    $companyName = $CLD_CON->GetArrayField("name");
    $contactEmail = $CLD_CON->GetArrayField("App_email");
    $username = $CLD_CON->GetArrayField("username");
    $password = $CLD_CON->GetArrayField("password");
}


if ($CLD_CON->Execute("UPDATE App_booths SET owner=$ownerID , CLD_date_tOwner='$date'  , CLD_subDistributor=$subDisID , CLD_Status=3 WHERE idBooth=$idBooth")) {
    $CLD_CON->Execute("UPDATE CLD_components SET owner=$ownerID , data_owner='$date' , Status=3 WHERE booth=$idBooth");
    $CLD_CON->OpenRs("SELECT idDongle FROM App_boothDongle WHERE idBooth=$idBooth AND datetimeF IS NULL ORDER BY datetimeS DESC");
    $coment2 = addslashes("Sold to $companyName");
    $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment2' , '$date' , $idBooth ,'$sn')");
    if ($CLD_CON->GetRsRows() < 2) {
        if ($CLD_CON->FetchArray()) {
            $dongleID = $CLD_CON->GetArrayField("idDongle");
            if ($CLD_CON2->Execute("UPDATE booths SET rental_id=$ownerID WHERE id=$dongleID")) {
                
            } else {
                  echo "The Dongle no ha canviat de propietari.El PhotoBooth si per aixo.";
            }
            $t = TRUE;
        } else {
            $t = TRUE;
        }
    } else {
        $t = TRUE;
        echo "The Dongle no ha canviat de propietari. El PhotoBooth si per aixo.";
    }
    
    $CLD_CON->OpenRs("SELECT serialnumber FROM CLD_components WHERE  booth=$idBooth");
    while($CLD_CON->FetchArray()){
        $serialnumber = $CLD_CON->GetArrayField("serialnumber");
        $coment2 = addslashes("Sold to $companyName into photobooth $sn");
        $CLD_CON2->Execute("INSERT INTO CLD_historyComponents(comment , data , component_sn) VALUES('$coment2' , '$date' , '$serialnumber')");
    }
} else {
    echo "ERROR , Cambiando el propietario de la màquina.";
}



if ($t) {
    require_once(G_PATH . 'common/mail.php');
    
    try{
        $to = $contactEmail;
        $to_str = $companyName; //if(strlen($mail_nom)) 

        $mail= new mail();
        $mail->addAdress($to, $to_str);
        $mail->setSubject("WELCOME TO DIGITAL CENTRE");
        $mail->setTemplate(G_PATH . "common/resources/templates/html/en/welcome-reminder.html");
        $us = $username;
        $ps = $password;
        $mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");
        $mail->addTemplateField("#USERNAME#", $us);
        $mail->addTemplateField("#PASSWORD#", $ps);
        $mail->addTemplateField("#NAMECOMPANY#", $companyName);
        $mail->applyTempplateFields();

        if (!$mail->Send()) {
            $mail_ret = 0;
        } else {
            $mail_ret = 1;
        }
    }
    catch (Exception $e){
        utils::log($e->getMessage(), "logMailer", "edit/functions/owner/newOwnerAndAssigBooth");
    }
}else{
     echo "The PhotoBooth owner has been changed , the welcome email is not sended (No have ContactEmail).";                        

}

    $CLD_CON2->OpenRs("SELECT * FROM App_booths WHERE idBooth=$idBooth");
    if ($CLD_CON2->FetchArray()) {
        $type = $CLD_CON2->GetArrayField("CLD_idType");
        $nameP = $CLD_CON2->GetArrayField("name");
        $version = $CLD_CON2->GetArrayField("version");
        $serialnumber = $CLD_CON2->GetArrayField("serialnumber");
    }
    $typeName = "Comprobar per serial number";
    if (!empty($type)) {
        $CLD_CON2->OpenRs("SELECT name FROM CLD_boothTypes WHERE id = $type");
        if ($CLD_CON2->FetchArray()) {
            $typeName = $CLD_CON2->GetArrayField("name");
        }
    }
    $CLD_CON2->OpenRs("SELECT x.rand_string FROM App_boothDongle y LEFT JOIN booths x ON x.id=y.idDongle WHERE y.idBooth=$idBooth AND datetimeF IS NULL");
    if ($CLD_CON2->FetchArray()) {
        $dongle = $CLD_CON2->GetArrayField("rand_string");
    }

    $mail_retMsg = "";
    $mail_ret = 0;
    
    require_once(G_PATH . 'common/mail.php');
    try{
        $mail2= new mail();
        //$mail2->addAdress("marina@dc-image.com", "Marina");
        $mail2->addAdressBCC("mon@dc-image.com", "Montserrat Canales");
        $mail2->addAdressBCC("accounts@dc-image.com", "DC. Digital Centre. Francesc");
        $mail2->setSubject("Informació nova venta Myphotocode");
        
        $mail2->setTemplate(G_PATH . "common/resources/templates/html/en/pb_assignPB.html");
        $mail2->addTemplateField("#user#",$us);
        $mail2->addTemplateField("#password#",$ps);
        $mail2->addTemplateField("#companyia#",$companyName);
        
        $mail2->addTemplateField("#text#", "");
        
        $mail2->addTemplateField("#serialnumber#",$serialnumber);
        $mail2->addTemplateField("#dongle#",$dongle);
        $mail2->addTemplateField("#typename#",$typeName);
        $mail2->addTemplateField("#PBname#",$nameP);
        $mail2->addTemplateField("#version#",$version);
        $mail2->applyTempplateFields();
        
        if (!$mail2->Send()) {
            $mail_ret = 0;
        } else {
            $mail_ret = 1;
        }
    }
    catch (Exception $e){
        utils::log($e->getMessage(), "logMailer", "edit/functions/owner/newOwnerAndAssigBooth");
    }
    ob_clean();
    echo "The owner has been changed";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
