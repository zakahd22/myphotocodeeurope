<?php


include '../conf.php';
include '../conexio.php';
$idOrder = 100192;
$fpagn=1;

$CLD_CON->OpenRs("SELECT  * FROM SHP_Comandes WHERE id=$idOrder AND n2=$fpagn");
if ($CLD_CON->FetchArray()) {
    $contact_id = $CLD_CON->GetArrayField("contact");   
}

$CLD_CON->OpenRs("SELECT * FROM SHP_Contacts WHERE id=$contact_id");
if ($CLD_CON->FetchArray()) {
    $customer_names = $CLD_CON->GetArrayField("Name") . " " . $CLD_CON->GetArrayField("Last_Name");
    $to = $CLD_CON->GetArrayField("email");
    $to_str= $customer_names;
}
$CLD_CON->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE comanda=$idOrder");
if ($CLD_CON->FetchArray()) {
    $photoCode = $CLD_CON->GetArrayField("photoCode");

}

//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml        
$mail_retMsg = "";
$mail_ret = 0;

//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        

require_once(G_PATH . 'common/mail.php');

$mail = new mail();

$mail->addAdress($to, $to_str);
$mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");

$mail->setSubject("MYPHOTOCODE SHOP");

$mail->setTemplate(G_PATH . "includes/emails/newOrder.html");

$mail->addTemplateField("###NAME###", $owner_name);
$mail->addTemplateField("###COMANDAID###", $id);
$mail->addTemplateField("###PHOTOCODE###", $rand_string);

$mail->applyTempplateFields();

if (!$mail->Send()) {
    utils::log($mail->retMsg, "logMailer", "PROVAEMAIL");
    $mail_ret = 0;
} else {
    $mail_ret = 1;
}
    
    

?>
