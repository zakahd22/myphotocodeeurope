<?php

/*
 * per a activar el dongle financing i confirmar-ho
 */
//si     $APP_common_no_idb = true;

require("common.php");
if(!$APP_dongleOK) return;

$myScript = "PBfcode_act.php";


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
if (isset($_REQUEST['cd'])) {    $codeAct = $_REQUEST['cd'];}
if (isset($_REQUEST['ii'])) {    $id = $_REQUEST['ii'];}
if (isset($_REQUEST['dn'])) {    $dateAct = $_REQUEST['dn'];}


if(!$id && !$codeAct){
    APP_fesLogDebbug("Error - $myScript, no params $id-$codeAct a $APP_idBooth, dongle $APP_rand_string","logFinancingCode");
    header("HTTP/1.0 404 Not Found");
    return;
}

if(!$id){
    APP_fesLogDebbug("$myScript, Peticio d'activacio amb codi $codeAct a $APP_idBooth, dongle $APP_rand_string","logFinancingCode");
}
else{
    APP_fesLogDebbug("$myScript, Confirmacio d'activacio amb codi $codeAct id $id a $APP_idBooth, dongle $APP_rand_string","logFinancingCode");
}
$signature = strtoupper(sha1($codeAct.$id.$dateAct.$APP_dongle.$APP_tact.$APP_seccode));
//control signatura:
//$badSig = false;
if($signature != $APP_sg){
    APP_fesLogDebbug("Error - $myScript, sg error local: $signature url:$APP_sg. " .  var_export($_REQUEST,true),"logFinancingCode");
    $badSig = true;
//    header("HTTP/1.0 404 Not Found");
//    return;
}

//enviem email sempre
$PB_mail = null;

APP_fesLogDebbug("$myScript, Abans require_once G_PATH . 'common/mail.php'","logFinancingCode");
require_once G_PATH . "common/mail.php";
APP_fesLogDebbug("$myScript, Despres require_once G_PATH . 'common/mail.php'","logFinancingCode");

$PB_mail = new mail();

if($badSig){
    $PB_mail->addAdress("victor@dc-image.com");
    //    $PB_mail->addAdress("marina@dc-image.com");
    //    $PB_mail->addAdressBCC("victor@dc-image.com");
    $PB_mail->setSubject("FinancingCode - Alert, act bad signature in PB $APP_idBooth");
    
    
    $cont = '<html><head><title></title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';

    $cont.= "<p>Intent d'activació o confirmació erroni, PB $APP_idBooth, dongle $APP_rand_string<p>";
    $cont.="<p>Paràmetres:</p>";
    $cont.="<p>".  var_export($_REQUEST,true)."</p>";
    $cont.= '</body></html>';
    
    $PB_mail->setBody($cont);
    if(!$PB_mail->send()){
        APP_fesLogDebbug("$myScript, ErrorMAIL04 ($APP_idBooth,$APP_rand_string): {$PB_mail->retMsg}","logFinancingCode");
    }
    header("HTTP/1.0 404 Not Found");
    return;
}
$PB_mail_cont = "";
//ara mirem la BdD
//confirmació de que tota la informació està introduida, de pas la llegim
//idDongle,startDate,allowTest,codeAct,dateAct,idPB,codeReset


//$ara = new DateTime("now");
$tact = $ara->format("YmdHis");
$dact = $ara->format("Ymd");
//$APP_araTimeSerial = $APP_BdD->myDateTimeSerial($ara);


//20170502idDongle!   $sql = "SELECT idDongle,codeReset FROM Fcode_dongle WHERE dateAct IS NULL AND codeAct = '$codeAct';";

//20170522 $sql = "SELECT idDongle,codeReset FROM Fcode_dongle WHERE dateAct IS NULL AND codeAct = '$codeAct' AND idDongle=$APP_idDongle;";//20170502idDongle! 
$sql = "SELECT idDongle,codeReset,idPB FROM Fcode_dongle WHERE dateAct IS NULL AND codeAct = '$codeAct' AND idDongle=$APP_idDongle;";//20170522 




//APP_fesLogDebbug("$myScript, TRACE01 sql: $sql","logFinancingCode");
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK) {
    APP_fesLogDebbug("$myScript, Error01 sql: $sql","logFinancingCode");
    echo "ko#code01";
    return;

}

if($APP_BdD->FetchRs()){
    $resp = 1;
    $camp=1;
    $idDongle = $APP_BdD->GetField($camp);$camp++;
    $codeReset = $APP_BdD->GetField($camp);$camp++;
    $idPB = $APP_BdD->GetField($camp);$camp++;//20170522
    
//    APP_fesLogDebbug("$myScript, TRACE01b idDongle: $idDongle, codeReset: $codeReset","logFinancingCode");
    
    if(strlen($codeReset) != 5){$resp= 0; $strMsg = "codeReset no vàlid ($codeReset) a reg $idDongle"; }
    
    
    if($idPB != $APP_idBooth){$resp= 0; $strMsg = "idPB no vàlid ($APP_idBooth), ha de ser $idPB; a reg $idDongle"; }//20170522
}
else{
    $resp= 0;
}
$APP_BdD->CloseRs();

if(!$resp){
    
//    $PB_mail->addAdress("victor@dc-image.com");
//        $PB_mail->addAdress("marina@dc-image.com");
    $PB_mail->addAdress("accounts@dc-image.com");
        
        $PB_mail->addAdressBCC("victor@dc-image.com");
    $PB_mail->setSubject("FinancingCode - Alert, not actived dongle whit codeAct $codeAct not found in PB $APP_idBooth");

    $cont = '<html><head><title></title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
    $cont.= "<p>Intent fallit d'activació o confirmació de registre amb codeAct $codeAct, PB $APP_idBooth, dongle $APP_rand_string<p>";
    $cont.="<p>$strMsg. Paràmetres:</p>";
    $cont.="<p>".  var_export($_REQUEST,true)."</p>";
    $cont.= '</body></html>';
    
    $PB_mail->setBody($cont);
    if(!$PB_mail->send()){
        APP_fesLogDebbug("$myScript, ErrorMAIL04 ($APP_idBooth,$APP_rand_string): {$PB_mail->retMsg}","logFinancingCode");
    }
    
    echo "ok#0";
    return;
}

//seguim 
//id,idDongle,dateEnd,gracePlays,code,puk,codeSent,pukSent,disabled FROM Fcode_reg

$nRegs = 0;
$sql = "SELECT id,dateEnd,gracePlays,code,puk FROM Fcode_reg WHERE idDongle=$idDongle ORDER BY dateEnd;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK) {
    APP_fesLogDebbug("$myScript, Error02 sql: $sql","logFinancingCode");
    echo "ko#code02";
    return;

}
$strMsg = "";
$strRegs= "";
$strRegsDates= "";
$strRegs2sign= "";
while($APP_BdD->FetchRs()){
    $camp=1;
    $idReg = $APP_BdD->GetField($camp);$camp++;
    //$dateEnd = $APP_BdD->GetFieldDate($camp);$camp++;
    $tmp = $APP_BdD->GetFieldDateTime($camp);$camp++;
    $dateEnd = $tmp->format("Ymd");
    
    if(!$dateEnd){$resp= 0; $strMsg = "Falta dateEnd a reg $idReg"; break;}
    $gracePlays = $APP_BdD->GetField($camp);$camp++;
    if(strlen($gracePlays) == 0){$resp= 0; $strMsg = "Falta gracePlays a reg $idReg"; break;}
    $code = $APP_BdD->GetField($camp);$camp++;
    if(strlen($code) != 5){$resp= 0; $strMsg = "code no vàlid ($code) a reg $idReg"; break;}
    $puk = $APP_BdD->GetField($camp);$camp++;
    if(strlen($puk) != 5){$resp= 0; $strMsg = "puk no vàlid ($puk) a reg $idReg"; break;}
    
    //sig sha1 de resp+codeReset+dateEnd+code+puk+dateEnd+code+puk ... +tact+APP_code
    $strRegs.= "#$idReg#$dateEnd#$gracePlays#$code#$puk"; 
    $strRegs2sign.= $dateEnd.$code.$puk; 
    $strRegsDates.= " " . APP_myDateStr($dateEnd,"es");
    
    $nRegs++;
}
$APP_BdD->CloseRs();

if(!$resp){
        APP_fesLogDebbug("$myScript, Alert, incomplete information in Fcode_reg ($APP_idBooth,$APP_rand_string): $strMsg. Paràmetres: ".  var_export($_REQUEST,true),"logFinancingCode");
    
//    $PB_mail->addAdress("victor@dc-image.com");
        //$PB_mail->addAdress("marina@dc-image.com");
        $PB_mail->addAdress("accounts@dc-image.com");
        $PB_mail->addAdressBCC("victor@dc-image.com");
    $PB_mail->setSubject("FinancingCode - Alert, incomplete information in Fcode_reg $idReg");

    $cont = '<html><head><title></title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
    $cont.= "<p>Intent fallit d'activació o confirmació de registre amb codeAct $codeAct, PB $APP_idBooth, dongle $APP_rand_string<p>";
    $cont.="<p>$strMsg. Paràmetres:</p>";
    $cont.="<p>".  var_export($_REQUEST,true)."</p>";
    $cont.= '</body></html>';
    
    $PB_mail->setBody($cont);
    if(!$PB_mail->send()){
        APP_fesLogDebbug("$myScript, ErrorMAIL04 ($APP_idBooth,$APP_rand_string): {$PB_mail->retMsg}","logFinancingCode");
    }
    
    echo "ok#0";
    return;
}

//seguim
$PB_mail_cont = '<html><head><title></title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';

if(!$id){
    $PB_mail_subj = "FinancingCode - Activation started in PB $APP_idBooth - $APP_rand_string";
    $PB_mail_cont = "<p>Petició d'activació amb codi $codeAct a $APP_idBooth, dongle $APP_rand_string</p>";
}
else{
    $PB_mail_subj = "FinancingCode - Activation confirmed in PB $APP_idBooth - $APP_rand_string";
    $PB_mail_cont = "<p>Confirmació d'activació amb codi $codeAct ($id) a $APP_idBooth, dongle $APP_rand_string</p>";
    $PB_mail_cont.= "<p>Dates: $strRegsDates.</p>";
    //update dateAct
//20170425    $sql = "UPDATE Fcode_dongle SET  dateAct=$APP_araTimeSerial WHERE idDongle=$APP_idDongle AND codeAct = '$codeAct';";
//20170522    $sql = "UPDATE Fcode_dongle SET  dateAct=$APP_araTimeSerial,idPB=$APP_idBooth WHERE idDongle=$APP_idDongle AND codeAct = '$codeAct';";//20170425
    $sql = "UPDATE Fcode_dongle SET  dateAct=$APP_araTimeSerial WHERE idDongle=$APP_idDongle AND codeAct = '$codeAct';";//20170522
    
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK){
        APP_fesLogDebbug("$myScript, Error03 sql: $sql","logFinancingCode");
        echo "ko#code03";
        return;
    }
    
    //20170502neteja INICI
    // 
    // query de DELETE FROM App_boothDongle:
    //     tots els registres amb idBooth = $APP_idBooth AND id <> $APP_idDongle
    //     tots els registres amb id = $APP_idDongle AND idBooth <> $APP_idBooth
    
    //20170725log INICI
    $sql = "SELECT idBooth,idDongle, `datetimeS`, `datetimeF` FROM App_boothDongle WHERE (idBooth = $APP_idBooth AND idDongle <> $APP_idDongle) OR (idDongle = $APP_idDongle AND idBooth <> $APP_idBooth);";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK) {
        APP_fesLogDebbug("$myScript, Error03bis sql: $sql","logFinancingCode");
        echo "ko#code03bis";
        return;
    }
        
    APP_fesLogDebbug("booth-dongle before clean","logFinancingCode");
    $strLog = "";
    while($APP_BdD->FetchRs()){
        $camp=1;
        $tmp = $APP_BdD->GetField($camp);$camp++; $strLog = "($tmp";
        $tmp = $APP_BdD->GetField($camp);$camp++; $strLog.= ",$tmp";
        //$dateEnd = $APP_BdD->GetFieldDate($camp);$camp++;
        $tmpDate = $APP_BdD->GetFieldDateTime($camp);$camp++;
        $tmp = $tmpDate->format("YmdHis"); $strLog.= ",$tmp";
        $tmpDate = $APP_BdD->GetFieldDateTime($camp);$camp++;
        $tmp = $tmpDate->format("YmdHis"); $strLog.= ",$tmp)";
        APP_fesLogDebbug($tmp,"logFinancingCode");
    }    
    $APP_BdD->CloseRs();
    //20170725log FINAL
    
    $sql ="DELETE FROM App_boothDongle WHERE (idBooth = $APP_idBooth AND idDongle <> $APP_idDongle) OR (idDongle = $APP_idDongle AND idBooth <> $APP_idBooth);";
    APP_fesLogDebbug("$myScript, Query candidat a eliminar altres emparellaments sql: $sql","logFinancingCode");

    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK){
        APP_fesLogDebbug("$myScript, Error04 sql: $sql","logFinancingCode");
        echo "ko#code04";
        return;
    }
    //20170502neteja FINAL
    
    
    
    //20170725log INICI
    $sql = "SELECT idBooth,idDongle, `datetimeS`, `datetimeF` FROM App_boothDongle WHERE (idBooth = $APP_idBooth AND idDongle <> $APP_idDongle) OR (idDongle = $APP_idDongle AND idBooth <> $APP_idBooth);";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK) {
        APP_fesLogDebbug("$myScript, Error04bis sql: $sql","logFinancingCode");
        echo "ko#code04bis";
        return;
    }
        
    APP_fesLogDebbug("booth-dongle after clean","logFinancingCode");
    $strLog = "";
    while($APP_BdD->FetchRs()){
        $camp=1;
        $tmp = $APP_BdD->GetField($camp);$camp++; $strLog = "($tmp";
        $tmp = $APP_BdD->GetField($camp);$camp++; $strLog.= ",$tmp";
        //$dateEnd = $APP_BdD->GetFieldDate($camp);$camp++;
        $tmpDate = $APP_BdD->GetFieldDateTime($camp);$camp++;
        $tmp = $tmpDate->format("YmdHis"); $strLog.= ",$tmp";
        $tmpDate = $APP_BdD->GetFieldDateTime($camp);$camp++;
        $tmp = $tmpDate->format("YmdHis"); $strLog.= ",$tmp)";
        APP_fesLogDebbug($tmp,"logFinancingCode");
    }    
    $APP_BdD->CloseRs();
    //20170725log FINAL
    
    
    
    
    
    
    
}
$PB_mail_cont.= '</body></html>';
$PB_mail->setSubject($PB_mail_subj);
$PB_mail->setBody($PB_mail_cont);


//    $PB_mail->addAdress("victor@dc-image.com");
        //$PB_mail->addAdress("marina@dc-image.com");
        $PB_mail->addAdress("accounts@dc-image.com");
        $PB_mail->addAdressBCC("victor@dc-image.com");


if(!$PB_mail->send()){
    APP_fesLogDebbug("$myScript, ErrorMAIL04: {$PB_mail->retMsg}","logFinancingCode");
}

//resposta    

if(!$id){
//Resposta: ok#resp#id#dateAct#codeReset#nReg#id#dateEnd#plays#code#puk ... #sig#tact
//·	resp: 0:  no es pot   1: si que es pot
//·	id, del registre de Fcode_dongle
//·	dateAct: AAAAMMDD des del servidor
//·	codeReset: codi de reset de puk
//·	nReg, nombre de registres debloqueig, per a cadasqun:
//·	id, del registre de Fcode_reg
//·	dateEnd, a partir de quan blocarà el funcionament
//·	plays, gracePlays
//·	code, pin
//·	puk 
//·	sig sha1 de resp+codeReset+dateEnd+code+puk+dateEnd+code+puk ... +tact+APP_code
//·	tact: AAAAMMDDHHMMSS segons hora del servidor
    
    //signatura de resposta:
    $signature = strtoupper(sha1($resp.$codeReset.$strRegs2sign.$tact.$APP_seccode));
    echo "ok#$resp#$idDongle#$dact#$codeReset#{$nRegs}{$strRegs}#$signature#$tact";
}
else{
//Resposta: ok#resp#rnd#sig#tact
//·	resp: 0:  no guardat   1: tot correcte
//·	rnd: codi random de 5 cars
//·	sig sha1 de resp+rnd+tact+APP_code
//·	tact: AAAAMMDDHHMMSS segons hora del servidor
    
    $rnd = rndm32(5);
    //signatura de resposta:
    $signature = strtoupper(sha1($resp.$rnd.$tact.$APP_seccode));
    echo "ok#$resp#$rnd#$signature#$tact";
}
    






?>
