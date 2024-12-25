<?php
require_once "../../common/global.php";
require_once G_PATH . 'common/conexio.php';
echo "<script src='functions.js'></script>";
$json = json_decode($_POST["data"], TRUE);

$code = $json[0];
$dades = $json[1];
echo "hola";
utils::log("SMS", "logasd");
//require_once 'esendex/src/autoload.php';
////$code = $_REQUEST['code'];
////$tel = $_REQUEST['telefon'];

//$tel = "699566517";
//$message = new \Esendex\Model\DispatchMessage(
//    "Photobooth", // Send from
//    $tel, // Send to any valid number
//    "www.myphotocode.com/index.php?code=$code",
//    \Esendex\Model\Message::SmsType
//);
//$authentication = new \Esendex\Authentication\LoginAuthentication(
//    "EX0259246", // Your Esendex Account Reference
//    "alex@dc-image.com", // Your login email address
//    "d1g1talc3ntr3" // Your password
//);
//$service = new \Esendex\DispatchService($authentication);
//$result = $service->send($message);
//
//echo $result->id();
//echo $result->uri();

//require_once 'textlocal.class.php';
//$code = "AMEG23596Z";
//$textlocal = new Textlocal('alex@dc-image.com', '1TAQuqR8vQQ-St4kuTrynYT5cAVawRMdEkHkFF6tKv');
//$numbers = array(699566517);
//$sender = 'Photobooth';
//$message = "www.myphotocode.com/index.php?code=$code";
//
//try {
//    $result = $textlocal->sendSms($numbers, $message, $sender);
//    print_r($result);
//} catch (Exception $e) {
//    die('Error: ' . $e->getMessage());
//}


// Account details
//$apiKey = urlencode('1TAQuqR8vQQ-St4kuTrynYT5cAVawRMdEkHkFF6tKv');
//
//// Message details
//$numbers = array(699566517);
//$sender = urlencode('Photobooth');
//$message = rawurlencode('This is your message');
//$numbers = implode(',', $numbers);
//
//// Prepare data for POST request
//$data = array('apikey'=> $apiKey,
//'numbers'=> $numbers,
//"sender"=> $sender,
//"message"=> $message);
//
//// Send the POST request with cURL
//$ch = curl_init('https://api.txtlocal.com/send/');
//curl_setopt($ch, CURLOPT_POST,true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
//$response = curl_exec($ch);
//curl_close($ch);
//
//// Process your response here
//echo $response;
?>