<?php

include '../sessio.php';
include '../conexio.php';
require_once(G_PATH . 'common/mail.php');

$ownerComment = addslashes($_POST['comment']);
$problem = $_POST['problem'];
$solved = $_POST['solved'];
$CLD_CON->Execute("UPDATE SAT_problems SET  solved=$solved WHERE id = $problem");
if ($solved == 0) {
    //Variables
    $type_Name = "";
    $booth = "";
    $booth_Name = "";
    $serialNumber = "";
    $type = "";
    $ownerID = "";
    $ownerName = "";
    $ownerCode = "";
    //Variables

    $CLD_CON->OpenRs("SELECT booth_id FROM SAT_problems WHERE id= $problem");
    if ($CLD_CON->FetchArray()) {
        $booth = $CLD_CON->GetArrayField("booth_id");
    }
    $CLD_CON->OpenRs("SELECT name , serialnumber , type , owner FROM App_booths WHERE idBooth=$booth");
    if ($CLD_CON->FetchArray()) {
        $booth_Name = $CLD_CON->GetArrayField("name");
        $serialNumber = $CLD_CON->GetArrayField("serialnumber");
        $type = $CLD_CON->GetArrayField("type");
        $ownerID = $CLD_CON->GetArrayField("owner");
    }
    $CLD_CON->OpenRs("SELECT name , code FROM rentals WHERE id=$ownerID");
    if ($CLD_CON->FetchArray()) {
        $ownerName = $CLD_CON->GetArrayField("name");
        $ownerCode = $CLD_CON->GetArrayField("code");
    }
    $CLD_CON->OpenRs("SELECT name FROM booth_types WHERE id=$type");
    if ($CLD_CON->FetchArray()) {
        $type_Name = $CLD_CON->GetArrayField("name");
    }
    $MAQUINA = $serialNumber . " - " . $booth_Name . " ( " . $type_Name . " ).";
    $OWNER = $ownerCode . " - " . $ownerName;
    $COMENTARI = $_POST['comments'];
    $contacte = $_POST['contact'];
    $email = $_POST['email'];
    $tel = $_POST['telefon'];
    $CONTACTE = $contacte . " , " . $email . " , $tel";
    $resultado = file_get_contents("http://www.digital-centre.com/emailsMasius/addEmail.php?mail=$email");

    $to = "joan@dc-image.com";
    $to_str = "joan@dc-image.com";
    // ob_start();
    
    $to = $email;
    $to_str = "$username";

    $mail = new mail();

    $mail->addAdress($to, $to_str);
    $mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");

    $mail->setSubject($subjecte);

    $mail->setTemplate(G_PATH . "common/resources/templates/html/en/noSolved.html");

    $mail->addTemplateField("###OWNER###", $OWNER);
    $mail->addTemplateField("###BOOTH###", $MAQUINA);
    $mail->addTemplateField("###COMENTARI###", $COMENTARI);
    $mail->addTemplateField("###CONTACTE###", $CONTACTE);

    $mail->applyTempplateFields();            
    
    if (!$mail->Send()) {
        
    } else {
        
    }

    $subjecte = "MyPhotoCode - SAT - Unsolved Problem $MAQUINA";
    $to = "$email";
    $to_str = "$OWNER";
    
    $mail = new mail();

    $mail->addAdress($to, $to_str);
    $mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");

    $mail->setSubject($subjecte);

    $mail->setTemplate(G_PATH . "common/resources/templates/html/en/noSolvedOwner.html");

    $mail->addTemplateField("###OWNER###", $OWNER);
    $mail->addTemplateField("###COMENTARI###", $COMENTARI);

    $mail->applyTempplateFields();
    
    if (!$mail->Send()) {
        
    } else {
        
    }
}
if (!empty($ownerComment)) {
    $CLD_CON->Execute("UPDATE SAT_problems SET comment ='$ownerComment' WHERE id = $problem");
}
?>
