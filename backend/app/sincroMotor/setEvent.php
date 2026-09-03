<?php
/*
 * Versió 9.0 27/10/2015
 Mètode: setEvent.php. crearà un event associat a l'owner i hi guardarà les dades d'event Manager, paràmetres:
idO	numèric	id d'owner a myphotocode
type	numèric	tipus de photobooth segons la taula CLD_boothTypes
m	cadena alfanumèrica	mail per a event manager
name	cadena alfanumèrica	nom de l'event manager
d	AAAAMMDD	data de l'event en format AAAAMMDD
n	cadena alfanumèrica	nom de l'event
 * 
 * NOTA: cal utf8_decode ????

retornarà
JSON:
·	'idEvent', BdD  `events`.`id` bigint(4)
·	'code', codi de control per al registre al Cloud BdD `events`.`CLD_SecurityCode`



Farem:
 * crear l'event amb dades de l'owner i manager. I codi de control
 * crear les carpetes i documents necessaris segons addNewEvent.php del Joan
 * crear un usb amb el model de photobooth
*/

$Mtr_script = "setEvent";
require("common.php"); 

//resposta
$resposta['status'] = 0;

$MTR_ok = true;      //PROVES ***************************************************************************

if(!$MTR_ok){
    $resposta['statusStr'] = $MTR_status;
    echo json_encode($resposta);
    return;
}

//paràmetres específics
if(isset($_REQUEST['idO'])){ $idOwner = $_REQUEST['idO'];} 
else {
    fesLog("Error - $Mtr_script, missing idO - Error01-");
    $resposta['statusStr'] = "Error01-idO";
    echo json_encode($resposta);
    return;
}

if(isset($_REQUEST['type'])){ $tipus = $_REQUEST['type'];} 
else {
    fesLog("Error - $Mtr_script, missing type - Error01-");
    $resposta['statusStr'] = "Error01-type";
    echo json_encode($resposta);
    return;
}

if(isset($_REQUEST['m'])){ $mailMn = str_replace("'","",$_REQUEST['m']);} //NOTA: cal utf8_decode ????
else {
    fesLog("Error - $Mtr_script, missing manager mail - Error01-");
    $resposta['statusStr'] = "Error01-m";
    echo json_encode($resposta);
    return;
}
if(isset($_REQUEST['name'])){ $nomMn = str_replace("'","",$_REQUEST['name']);} //NOTA: cal utf8_decode ????
else {
    fesLog("Error - $Mtr_script, missing manager name - Error01-");
    $resposta['statusStr'] = "Error01-name";
    echo json_encode($resposta);
    return;
}


if(isset($_REQUEST['d'])){ $dataEvent = $_REQUEST['d'];} 
else {
    fesLog("Error - $Mtr_script, missing event date - Error01-");
    $resposta['statusStr'] = "Error01-d";
    echo json_encode($resposta);
    return;
}

if(isset($_REQUEST['n'])){ $nomEvent = str_replace("'","",$_REQUEST['n']);}  //NOTA: cal utf8_decode ????
else {
    fesLog("Error - $Mtr_script, missing type - Error01-");
    $resposta['statusStr'] = "Error01-n";
    echo json_encode($resposta);
    return;
}



//torno al control per signatura
$signature = strtoupper(sha1($idOwner.$dataEvent.$MTR_tact.$MTR_MtrControl));
if($signature != $MTR_sg){
    fesLog("Error - $Mtr_script, to code: ".$idOwner.$dataEvent.$MTR_tact.$MTR_MtrControl."  - Error02");
    fesLog("Error - $Mtr_script, sg error local: $signature url:$MTR_sg  - Error02");
    $resposta['statusStr'] = "Error02";
    echo json_encode($resposta);
    return;
}

//comprovem si existeix l'owner   ????? -> de moment no


//Nota: no cal assegurar-se de que sigui únic perquè el registre porta associat l'idEvent
//$codeOk = false;
//while(!$codeOk){
//    $code = rndm32(10);
//    
//    $sql = "SELECT *  FROM events WHERE CLD_SecurityCode='$code'; ";
//    $esOK = $APP_BdD->OpenRs($sql);
//    if(!$esOK){
//        fesLog("Error - $Mtr_script, Error03_code: $sql.");
//        $resposta['statusStr'] = "Error03_code";
//        echo json_encode($resposta);
//        return;
//    }
//    if($APP_BdD->FetchRs()){
//        $codeOk = true;
//    }
//    $APP_BdD->CloseRs();
//}
$code = rndm32(10);

$sql = "INSERT INTO events SET rental_id=$idOwner ";
$sql.= " , start_date=$dataEvent";
$sql.= " , title='$nomEvent'";
$sql.= " , private=1";
$sql.= " , autocreated=0";
$sql.= " , available=1";
$sql.= " , CLD_date_lastPhoto=".$APP_BdD->myDateSerial($dataEvent);

$sql.= " , CLD_invitedName='$nomMn'";
$sql.= " , CLD_invitedEmail='$mailMn'";
$sql.= " , CLD_SecurityCode='$code'";
$sql.= ";";


    fesLog("TRACE - $Mtr_script, sql: $sql.");


$idevent = $APP_BdD->ExecuteInsert($sql);
if(!$idevent){
    fesLog("Error - $Mtr_script, can not insert event - Error03- $sql");
    $resposta['statusStr'] = "Error03";
    echo json_encode($resposta);
    return;
}

//creeem el directori del event
    $eventFolder = $dataEvent . $idevent;
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/events/" . $eventFolder, 0777);

//tipus de PB i lletra de software    
    $sql = "SELECT `char` FROM CLD_boothTypes WHERE id=$tipus;";
    
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        fesLog("Error - $Mtr_script, Error04_type: $sql.");
        $resposta['statusStr'] = "Error04_type";
        echo json_encode($resposta);
        return;
    }
    if($APP_BdD->FetchRs()){
        $charTipus = $APP_BdD->GetField(1);
    }
    $APP_BdD->CloseRs();

//usb

$sql = "INSERT INTO usbs SET rental_id=$idOwner ";
$sql.= " , creation_date=$dataEvent";
$sql.= " , title='-USB$dataEvent-'";
$sql.= " , boothtype_char='$charTipus'";
$sql.= " , event_id=$idevent";
$sql.= " , available=1";
$sql.= " , CLD_idTypeBooth=$tipus";
$sql.= ";";

    fesLog("TRACE - $Mtr_script, sql: $sql.");

$idUSB = $APP_BdD->ExecuteInsert($sql);
if(!$idUSB){
    fesLog("Error - $Mtr_script, can not insert usb - Error05- $sql");
//    $resposta['statusStr'] = "Error03";
//    echo json_encode($resposta);
//    return;
}
else{
//cal crear carpetes, segons addNewEvent

    $USBFolder = $dataEvent . $idUSB;
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder, 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdDownload", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdDownload/myphotocode", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Welcome", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Welcome/Custom", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Welcome/Random", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Bye", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Bye/Custom", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Bye/Random", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdUpload/Frames", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdEvents", 0777);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdEvents/CustomShots", 0777);
    if (strcasecmp($charTipus, 'A') == 0) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdEvents/Wedding", 0777);
        mkdir($_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdEvents/Wedding/Header", 0777);
    }

    $archivo = $_SERVER['DOCUMENT_ROOT'] . "/usbs/" . $USBFolder . "/PhotoIdDownload/myphotocode/myphotocode.dat";
    $fp = fopen($archivo, "w+");
    $string = $idevent;
    fputs($fp, $string);
    fclose($fp);

}
//resposta
$resposta['idEvent'] = $idevent;
$resposta['idUsb'] = $idUSB;
$resposta['code'] = $code;

$resposta['status'] = 1;


//enviament del missatge 
//NOTA: es podria separar en un altre mètode

//copio codi de reInviteManager.php


$URL_LOGIN = "https://myphotocode.com/"; //NOTA: cal fer servir les variables globals d'Aleix


$url = $URL_LOGIN . "register.php?event=$idevent";

$fitxer = "../../includes/emails/eventManeger.html";
$fh = fopen($fitxer, 'r');
$message = fread($fh, filesize($fitxer));
fclose($fh);

$message = str_replace("#INVITEDNAME#", $nomMn, $message);
$message = str_replace("#SECURITYCODE#", $code, $message);
$message = str_replace("#URL#", $url, $message);
$message = str_replace("#LOGINURL#", $URL_LOGIN, $message);
$message = str_replace("#TITLE#", $nomEvent, $message);

$to = $mailMn;
$to_str = $nomMn; //if(strlen($mail_nom)) 
$from = "noreply@myphotocode.com";
$from_str = "DC PhotoBooth Platform";
$host = "smtp.sendgrid.net";
//	$username = "noreplay@myphotocode.com";
$username = "noreply@myphotocode.com";
$password = "cloudPBma1l";

require_once('../../includes/classes/class.phpmailer.php');

$mail = new PHPMailer();
$mail->CharSet = "utf-8";
$mail->PluginDir = "";
$mail->isSendMail();// telling the class to use SMTP
$mail->Host = $host;
$mail->SMTPAuth = true;   // enable SMTP authentication
$mail->Port = 587; // set the SMTP port for the GMAIL server
//$mail->Port = 25; // Port server 1and1
$mail->Username = $username; // SMTP account username
$mail->Password = $password; // SMTP account password
$mail->SetFrom($from, $from_str);
$mail->Timeout = 30;
$mail->ClearReplyTos();
$mail->Subject = "Event Manager Registration";
$mail->AddAddress($to, $to_str);
$mail->AddBCC("mon@dc-image.com");
$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
$mail->MsgHTML($message);

if (!$mail->Send()) {
//    echo "ERROR";
$resposta['email'] = 0;
} else {
//    echo "OK";
$resposta['email'] = 1;
}

echo json_encode($resposta);


?>
