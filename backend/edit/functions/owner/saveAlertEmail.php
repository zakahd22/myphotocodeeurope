<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$email = $_POST['email'];
$ID = $_POST['id'];
$userName = $_POST['name'];

$baseController = new baseController;
$baseController->createModel('rentals');

$token = utils::get_rndm64(32);
//$token = rawurlencode($token);

$dateLimit = utils::get_datetime();
$dateLimit = utils::modify_date($dateLimit, '+2 day', 'Y-m-d H:i:s');

$updates = array('App_email' => $email, 'token_Sec' => $token, 'token_SecDate' => $dateLimit, 'ValidatedAlertEmail' => 0,);
$upd = $baseController->rentalsModel->updateRental($ID, $updates);

while(!$upd){
    $token  = utils::get_rndm64(32);
    $dateLimit = utils::get_datetime();
    $dateLimit = utils::modify_date($dateLimit, '+2 day', 'Y-m-d H:i:s');
    
    $updates = array('App_email' => $email, 'token_Sec' => $token, 'token_SecDate' => $dateLimit, 'ValidatedAlertEmail' => 0,);
    $upd = $baseController->rentalsModel->updateRental($ID, $updates);
    
    $i++;
    if($i == 10){
         return null;
    }
}

if ($upd) {
    $token = rawurlencode($token);
    $urlActivate = G_PAGE."/edit/functions/owner/validateEmail.php?token={$token}";
    
    require_once(G_PATH . 'common/mail.php');
    $mail= new mail();
    $mail->addAdress($email, $userName);
    $mail->setSubject("Validate Email");
    $mail->setTemplate(G_PATH . "common/resources/templates/html/en/validateOwnerEmail.html");
    $mail->addTemplateField("#USERNAME#", $userName);
    $mail->addTemplateField("#URL#", $urlActivate);
    $mail->applyTempplateFields();  
    if(!$mail->send()){
        utils::log("Mail Validate Email: ".$mail->retMsg, G_PATH . "log/logMail", "saveAlertEmail");
        //utils::log("Validate URL: ".$urlActivate, G_PATH . "log/logMail", "saveAlertEmail");
        echo (G_TEST? "<a href='{$urlActivate}' target='_blank'>Activation Link :)</a>" : 'ERROR 01');
    }
    else {
        echo "OK";
    }
} 
else {
    echo "ERROR";
}

