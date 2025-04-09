<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$ID = $_POST['id'];
$event = $baseController->eventsModel->getEvent($ID);
if ($event) {
    $inName = $event[0]["CLD_invitedName"];
    $inEmail = $event[0]["CLD_invitedEmail"];
    $securityCode = $event[0]["CLD_SecurityCode"];
    $titleEvent = $event[0]["title"];
}

$url = G_PAGE . "register.php?event=$ID"; 
$result = false;
$sendMail = true;
$sendMail2 = true;

require_once(G_PATH . 'common/mail.php');
    
$mail= new mail();
$mail->addAdress($inEmail, $inName);
$mail->setSubject("Event Manager Registration");

$mail->setTemplate(G_PATH . "common/resources/templates/html/en/eventManeger_pla.html");
$mail->addTemplateField("#INVITEDNAME#", $inName);
$mail->addTemplateField("#SECURITYCODE#", $securityCode);
$mail->addTemplateField("#URL#", $url);
$mail->addTemplateField("#LOGINURL#", G_PAGE);
$mail->addTemplateField("#TITLE#", $titleEvent);
$mail->applyTempplateFields();

if(!$mail->Send()){
    utils::log("Not sent email eventManager_pla.html", G_PATH . "log/emailEventManager", "inviteEventManager");
    utils::log("Event: ".$titleEvent." ID: $ID", G_PATH . "log/emailEventManager", "inviteEventManager");
    utils::log("Mail: ".$inEmail, G_PATH . "log/emailEventManager", "inviteEventManager");
    $sendMail = false;
}

$mail2= new mail();
$mail2->addAdress($inEmail, $inName);
$mail2->setSubject("Event Manager Registration");

$mail2->setTemplate(G_PATH . "common/resources/templates/html/en/eventManeger.html");
$mail2->addTemplateField("#INVITEDNAME#", $inName);
$mail2->addTemplateField("#SECURITYCODE#", $securityCode);
$mail2->addTemplateField("#URL#", $url);
$mail2->addTemplateField("#LOGINURL#", G_PAGE);
$mail2->addTemplateField("#TITLE#", $titleEvent);
$mail2->applyTempplateFields();

if (!$mail2->Send()) {
    $sendMail2 = false;
    utils::log("Not sent email eventManager.html", G_PATH . "log/emailEventManager", "inviteEventManager");
    utils::log("Event: ".$titleEvent." ID: $ID", G_PATH . "log/emailEventManager", "inviteEventManager");
    utils::log("Mail: ".$inEmail, G_PATH . "log/emailEventManager", "inviteEventManager");
}

if($sendMail || $sendMail2){
//    $updates = array('CLD_invitedName'=>$inName , 'CLD_invitedEmail'=>$inEmail , 'CLD_SecurityCode'=>$securityCode);
//    if($baseController->eventsModel->updateEvent($ID, $updates)){
        $result = true;
//    }
//    else {
//        utils::log("Email Send, but not Update Event", G_PATH . "log/emailEventManager", "inviteEventManager");
//        utils::log("Event: ".$titleEvent." ID: $ID", G_PATH . "log/emailEventManager", "inviteEventManager");
//        utils::log("Mail: ".$inEmail, G_PATH . "log/emailEventManager", "inviteEventManager");
//    }
}

echo ($result? "OK":"ERROR");

?>
