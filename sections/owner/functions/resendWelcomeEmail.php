<?php

//include '../../../common/global.php';
//require_once G_PATH . "common/Classes/baseController.php";    //Per fer funcionar ORM

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

//$baseController = new baseController();
//$baseController->createModel('CLD_Login');


$ownerID = $_POST['id'];

//$a = $baseController->CLD_LoginModel->getAllFromLogin($ownerID);

$CLD_CON->OpenRs("SELECT l.username , l.password  , r.App_email ,r.name FROM CLD_Login l LEFT JOIN rentals r ON r.id=l.id_user WHERE l.id_user = $ownerID AND l.userType=4");
if ($CLD_CON->FetchArray()) {
    $to = $CLD_CON->GetArrayField("App_email");
    $username = $CLD_CON->GetArrayField("username");
    $password = $CLD_CON->GetArrayField("password");
    $ownerCompanyName = $CLD_CON->GetArrayField("name");
}

$to_str = $ownerCompanyName; //if(strlen($mail_nom))

//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml        
$mail_retMsg = "";
$mail_ret = 0;

//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        

require_once(G_PATH . 'common/mail.php');
            
$mail = new mail();

$mail->addAdress($to, $to_str);
$mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");
$mail->setSubject("WELCOME TO DIGITAL CENTRE");

$mail->setTemplate(G_PATH . "common/resources/templates/html/en/welcome.html");
$mail->addTemplateField("#USERNAME#", $username);
$mail->addTemplateField("#PASSWORD#", $password);
$mail->addTemplateField("#NAMECOMPANY#", $ownerCompanyName);

$mail->applyTempplateFields();

if (!$mail->Send()) {
    utils::log($mail->retMsg, "logMailer", "resendWelcomeEmail");
    $mail_ret = 0;
} else {
    $mail_ret = 1;
}


//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml    


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
