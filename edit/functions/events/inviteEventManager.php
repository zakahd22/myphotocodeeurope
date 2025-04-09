<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$ID = $_POST['id'];
$inName = $_POST['inName'];
$inEmail = $_POST['inEmail'];
$x=true;

function generarCodigo($longitud) {
    $key = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
    $max = strlen($pattern) - 1;
    for ($i = 0; $i < $longitud; $i++)
        $key .= $pattern[mt_rand(0, $max)];
    return $key;
}

$event = $baseController->eventsModel->getEvent($ID);
if ($event) {
    $titleEvent = $event[0]["title"];
}

if (empty($inName)) {
    $x = flase;
    echo  " - Omple el camp Invited Name - ";
}

if (empty($inEmail)) {
    $x = false;
    echo  " - Omple el camp Invited Email - ";
} else {
    if (!filter_var($inEmail, FILTER_VALIDATE_EMAIL)) {
        echo  " - El correu no es correcte - ";
        $x= false;
    }
}

$result = false;
$sendMail = true;
$sendMail2 = true;
if($x){
    $securityCode = strtoupper(generarCodigo(10));
    $url = G_PAGE . "register.php?event=$ID";
     
    $to = $inEmail;
    $to_str = $inName;
    
    require_once(G_PATH . 'common/mail.php');

    try{
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

        if(G_TEST != 1){
            if(!$mail->Send()){
                $sendMail = false;
            }
        }
    } catch (Exception $e){
        utils::log("Not sent email eventManager_pla.html", G_PATH . "log/emailEventManager", "inviteEventManager");
        utils::log("Event: ".$titleEvent." ID: $ID", G_PATH . "log/emailEventManager", "inviteEventManager");
        utils::log("Mail: ".$inEmail, G_PATH . "log/emailEventManager", "inviteEventManager");
    }
    
    try{
        $mail2= new mail();
        $mail2->addAdress($to, $to_str);
        $mail2->setSubject("Event Manager Registration");
        $mail2->setTemplate(G_PATH . "common/resources/templates/html/en/eventManeger.html");
        $mail2->addTemplateField("#INVITEDNAME#", $inName);
        $mail2->addTemplateField("#SECURITYCODE#", $securityCode);
        $mail2->addTemplateField("#URL#", $url);
        $mail2->addTemplateField("#LOGINURL#", G_PAGE);
        $mail2->addTemplateField("#TITLE#", $titleEvent);
        $mail2->applyTempplateFields();

        if(G_TEST != 1){
            if(!$mail2->Send()) {
                $sendMail2 = false;
            }        
        }
    } catch (Exception $e){
        utils::log("Not sent email eventManager.html", G_PATH . "log/emailEventManager", "inviteEventManager");
        utils::log("Event: ".$titleEvent." ID: $ID", G_PATH . "log/emailEventManager", "inviteEventManager");
        utils::log("Mail: ".$inEmail, G_PATH . "log/emailEventManager", "inviteEventManager");
    }

    if($sendMail || $sendMail2){
        $updates = array('CLD_invitedName'=>$inName, 'CLD_invitedEmail'=>$inEmail, 'CLD_SecurityCode'=>$securityCode);
        if($baseController->eventsModel->updateEvent($ID, $updates)){
            $result = true;
        }
        else {
            utils::log("Email Send, but not Update Event", G_PATH . "log/emailEventManager", "inviteEventManager");
            utils::log("Event: ".$titleEvent." ID: $ID", G_PATH . "log/emailEventManager", "inviteEventManager");
            utils::log("Mail: ".$inEmail, G_PATH . "log/emailEventManager", "inviteEventManager");
        }
    }

}

echo ($result? "OK":"ERROR");