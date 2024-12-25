<?php
$esPBupload = false;
$esPBuploadOk = false;


//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar

if(!isset($_SERVER['HTTP_USER_AGENT'])){ return;}

//if($_SERVER['HTTP_USER_AGENT'] != "") 
//$userAgentProduct = "DigitalCentrePBupload";

//$pos = strpos($_SERVER['HTTP_USER_AGENT'], "DigitalCentrePBupload");
//if ($pos === false) return;

//123456789012345678901
//DigitalCentrePBupload
$usragnt = substr($_SERVER['HTTP_USER_AGENT'], 0, 21);
//APP_fesLogDebbug("USER_AGENT: $usragnt", "logs/logPBupload.dat");

//if(substr($_SERVER['HTTP_USER_AGENT'], 21) != "DigitalCentrePBupload") return;
if($usragnt != "DigitalCentrePBupload"){ return;}

APP_fesLogDebbug("USER_AGENT: {$_SERVER['HTTP_USER_AGENT']} URI: {$_SERVER['REQUEST_URI']}", "logs/logPBupload.dat");

$esPBupload = true;
$msgPBupload = "code 101";
if(!isset($_REQUEST['Up1'])){ $msgPBupload = "code 101"; return;}
if(!isset($_REQUEST['Up2'])){ $msgPBupload = "code 102"; return;}
if(!isset($_REQUEST['Up3'])){ $msgPBupload = "code 103"; return;}

if(!isset($_REQUEST['Upt'])){$msgPBupload = "code 104"; return;}

$UpVer = $_REQUEST['Up1'];
$UpCc = $_REQUEST['Up2'];
$UpSg = $_REQUEST['Up3'];

$UpTt = $_REQUEST['Upt'];

switch($UpVer){
    case "1":
        $UpKey = "FDSFDGFDS";
        $UpDate = 20220610;
        break;
    default:
        $msgPBupload = "code 105"; 
        return;
}

$ara = new DateTime("now");
$avui = intval($ara->format("Ymd"));
if($avui > $UpDate){
    $msgPBupload = "date limit exceeded";
    return;
}

$signature = strtoupper(sha1($UpTt.$UpVer.$UpCc.$UpKey));
if($signature != $UpSg){ 
    APP_fesLogDebbug("signature: $signature UpSg: $UpSg", "logs/logPBupload.dat");
    $msgPBupload = "code 106"; 
    return;
    
}

$esPBuploadOk = true;
