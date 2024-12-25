<?php

/*
 * per a demanar si pot fer una partida de test
 */
//si     $APP_common_no_idb = true;

require("common.php");
if(!$APP_dongleOK) return;

$myScript = "PBfcode_test";


if(!$APP_sg){
    APP_fesLogDebbug("Error - $myScript, sg is empty","logFinancingCode"); 
     header("HTTP/1.0 404 Not Found");
//    echo "Error sg"; 
    return;
    
}
if(!$APP_tact){
    APP_fesLogDebbug("Error - $myScript, tact is empty","logFinancingCode");
     header("HTTP/1.0 404 Not Found");
//    echo "Error tact"; 
    return;
    
}

//paràmetres
if (isset($_REQUEST['cd'])) {    $codePlay = $_REQUEST['cd'];}


//signatura:
$badSig = false;
$signature = strtoupper(sha1($codePlay.$APP_dongle.$APP_tact.$APP_seccode));
if($signature != $APP_sg){
    APP_fesLogDebbug("Error - $myScript, sg error local: $signature url:$APP_sg","logFinancingCode");
    $badSig = true;
}

if($codePlay){
    APP_fesLogDebbug("$myScript, Confirmacio de partida de prova $codePlay a $APP_idBooth, dongle $APP_rand_string","logFinancingCode");
////envio mail
//$mail_replayto = "main@dc-image.com";
//$mail_copia = "marina@dc-image.com";
    $PB_mail = null;


    require_once G_PATH . "common/mail.php";

    $PB_mail = new mail();

//    $PB_mail->addAdress("victor@dc-image.com");
//    $PB_mail->addAdress("marina@dc-image.com");
    $PB_mail->addAdress("accounts@dc-image.com");
    $PB_mail->addAdressBCC("victor@dc-image.com");
    $PB_mail->setSubject("FinancingCode - Test play done in PB $APP_idBooth - $APP_rand_string");
    
    $cont = '<html><head><title></title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
    $cont.= "<p>A test play has been done in PB $APP_idBooth, dongle $APP_rand_string<p>";
    $cont.="<p>Play code is $codePlay</p>";
    $cont.= '</body></html>';
    
    $PB_mail->setBody($cont);


    if(!$PB_mail->send()){
        APP_fesLogDebbug("$myScript, ErrorMAIL04: {$PB_mail->retMsg}","logFinancingCode");
    }
}
else{
    APP_fesLogDebbug("$myScript, Peticio de partida de prova a $APP_idBooth, dongle $APP_rand_string","logFinancingCode");
}

if($badSig){
    header("HTTP/1.0 404 Not Found");
    return;
}

//comprovem si pot fer-ne
//20170502idDongle!  $sql = "SELECT * FROM Fcode_dongle WHERE allowTest <> 0 AND dateAct IS NULL";
$sql = "SELECT * FROM Fcode_dongle WHERE allowTest <> 0 AND dateAct IS NULL AND idDongle=$APP_idDongle";//20170502idDongle!  

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK) {
    APP_fesLogDebbug("$myScript, Error sql: $sql","logFinancingCode");
    echo "ko#code01";
    return;

}

if($APP_BdD->FetchRs()){
    $resp = "1";
}
else{
    $resp= "0";
}
$APP_BdD->CloseRs();

$rnd = rndm32(5);

$ara = new DateTime("now");
$tact = $ara->format("YmdHis");

//resposta
//Resposta: ok#resp#rnd#sig#tact
//·	resp: 0:  no pot   1: si que pot
//·	rnd: codi random de 5 cars
//·	sig sha1 de resp+rnd+tact+APP_code
//·	tact: AAAAMMDDHHMMSS segons hora del servidor


//signatura de resposta:
$signature = strtoupper(sha1($resp.$rnd.$tact.$APP_seccode));


echo "ok#$resp#$rnd#$signature#$tact";

?>
