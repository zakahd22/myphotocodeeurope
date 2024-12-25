<?php
//creat el 23/05/2013
//quan entra info amb un mètode diferent de PB_Error.php haurem de donar tots els errors associats al PB com a Solved

/**
 * 2022-05-19
 * Afegim #tractamentErrorsPartida
 * Farem insert de registre indicant que sabem que tots els devices estàn ok perquè hem rebut partida.
 * Si hi ha:
 *  - algún sent=0 
 * -  i cap (sent=0, ok=1, device=0)
 */

//si s'ha cridat el métode PBnew_Play
if($isPBnew_Play==1){
    //Per Printer: Nomes assegurem que tot es correcte si hi ha nova partida
    //si no s'ha cridat el métode PBnew_Play, però el device no és printer en tenim prou que no sigui PBnew_alive (si es PBnew_alive no arriba a aquest punt del codi)
    $sql = "SELECT * FROM App_infoDeviceMgt WHERE idBooth = $APP_idBooth AND sent=0 AND device>0";
    
}else{
    //mirem si hi ha algún error de device actiu que no sigui printer. 
    //Per altres dispositius en tenim prou en que superi el check inicial
    $sql = "SELECT * FROM App_infoDeviceMgt WHERE idBooth = $APP_idBooth AND sent=0 AND device>0 AND device<3";
    
    
}


    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        echo "Error - Common alertErrorToOk - code 00 $sql";
        $APP_common_error = true;
        return;
    }

    $nInfoDM = $APP_BdD->GetRsRows();

    $APP_BdD->CloseRs();

    if($nInfoDM){
        
        $sql = "INSERT INTO App_infoDeviceMgt SET `when`=$APP_inTimeSerial, idBooth = $APP_idBooth, idDongle = $APP_idDongle, device = 0, resp = 'Users can play', ok=1, db_time=$APP_araTimeSerial";
        if($APP_tactSql) $sql.=", pbs_time=$APP_tactSql "; 
        
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK) {
            echo "Error - Common alertError - code 03 $sql.";
            $APP_common_error = true;
            return;

        }
    }






//23/05/2013 quan entra info amb un mètode diferent de PB_Error.php haurem de donar tots els errors associats al PB com a Solved
$APP_common_error = false;

//mirem si hi ha algún error actiu
    $sql = "SELECT typeAlert FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert BETWEEN 51 AND 59 AND estat<2;";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        echo "Error - Common alertErrorToOk - code 00 $sql";
        $APP_common_error = true;
        return;
    }
    $nErrors = 0;
    while($APP_BdD->FetchRs()){
        $array_typeAlert[$nErrors] = $APP_BdD->GetField(1); 
        $nErrors++;
    }
    $APP_BdD->CloseRs();
    
    //proves 
   // $calTreure = true;
    
//20170220apns    include(dirname(__FILE__) . '/../../easyapns/src/php/APP_apns.php');//20140422 estava al final i ha d'estar aqui !!!!!!!!!! 
    include 'APP_common_checkAlerts.php';//20140422 estava al final i també ha d'estar aqui !!!!!!!!!! 
    
    
    
    
    if($nErrors){

 //20140331 INICI     
 //ha d'anar més abaix   
//       //cal actualitzar l'estat del booth, també actualitzarà $APP_common_badge
//        include 'APP_common_checkAlerts.php';
//        if($APP_common_error) return;
//
//    //????    include("../../easyapns/src/php/APP_apns.php");
//        include(dirname(__FILE__) . '/../../easyapns/src/php/APP_apns.php');
//       // $APP_nameBooth
//20140331 FINAL        

//20140421mails INICI        
        $MAILNS_cont = "";
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        
//20140421mails FINAL        
        for($i = 0; $i<$nErrors; $i++){
            $sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = $array_typeAlert[$i];";
            $esOK = $APP_BdD->Execute($sql);
            if(!$esOK) {
                echo "Error - Common alertErrorToOk - code 01 $sql.";
                $APP_common_error = true;
                return;
            }
            $APP_common_badge--;
//20170220apns            $APNS_MessageAdded = APNS_addOkError($APP_idRental,$APP_idBooth,$APP_nameBooth,$APP_common_badge,$array_typeAlert[$i]);
            
//20140421mails INICI        
    switch($array_typeAlert[$i]){
        case 51:
//20150625location            $MAILNS_cont.= "<p>Printer OK at $APP_nameBooth</p>";
//20150629PBid            $MAILNS_cont.= "<p>Printer OK at $APP_nameBooth. Location name: $APP_locationBooth</p>";//20150625location            
            $MAILNS_cont.= "<p>Printer OK at $APP_nameBooth. Location name: $APP_locationBooth. Photobooth ID: $APP_idBooth.</p>";////20150629PBid            
            break;
        case 52:
//20150625location            $MAILNS_cont.= "<p>Paper OK at $APP_nameBooth</p>";
//20150629PBid            $MAILNS_cont.= "<p>Paper OK at $APP_nameBooth. Location name: $APP_locationBooth</p>";//20150625location            
            $MAILNS_cont.= "<p>Paper OK at $APP_nameBooth. Location name: $APP_locationBooth. Photobooth ID: $APP_idBooth.</p>";////20150629PBid            
            break;
        case 53:
//20150625location            $MAILNS_cont.= "<p>I/O Board OK at $APP_nameBooth</p>";
//20150629PBid            $MAILNS_cont.= "<p>I/O Board OK at $APP_nameBooth. Location name: $APP_locationBooth</p>";//20150625location            
            $MAILNS_cont.= "<p>I/O Board OK at $APP_nameBooth. Location name: $APP_locationBooth. Photobooth ID: $APP_idBooth.</p>";////20150629PBid            
            break;
        case 54:
//20150625location           $MAILNS_cont.= "<p>Camera OK at $APP_nameBooth</p>";
//20150629PBid           $MAILNS_cont.= "<p>Camera OK at $APP_nameBooth. Location name: $APP_locationBooth</p>";//20150625location            
           $MAILNS_cont.= "<p>Camera OK at $APP_nameBooth. Location name: $APP_locationBooth. Photobooth ID: $APP_idBooth.</p>";////20150629PBid            
           break;
    }
//                        $mail_cont.= "<p>Photobooth ID: $APP_idBooth</p>";//20150629PBid
            
//20140421mails FINAL        
        }
        
//20140421mails INICI      
        
        if($nErrors){
            
            
//            APP_fesLog("eMail notification cont: $MAILNS_cont");

            
            //cal enviar un email a l'owner
            $sql = "SELECT name,`App_email` FROM rentals WHERE id=$APP_idRental; ";
//$fh = fopen("logAPP-email.dat", 'w');
//fwrite($fh, "eMail notification cont: $MAILNS_cont\rSql: $sql\r");
//fclose($fh);
            $esOK = $APP_BdD->OpenRs($sql);
            if($esOK){
                 if($APP_BdD->FetchRs()){
                    $mail_nom =  $APP_BdD->GetField(1);
                    $mail_email =  $APP_BdD->GetField(2);
                    
                    if($mail_email){
    $mail_replayto = "main@dc-image.com";
//20141028alerts    $mail_email = "main@dc-image.com";//Periode de proves!!!!!!!
//20141028alerts                        $mail_nom.=  "-TEST";//Periode de proves!!!!!!!
    $mail_copia = "main@dc-image.com";//20141028alerts

                        $mail_remitent = "main@dc-image.com";//20150626
                        $mail_nomremitent = "DC Alerts Platform";

                        $mail_copia1 = "";
                        $mail_copianom1 = "";
                        $mail_copia2 = "";
                        $mail_copianom2 = "";
                        $mail_subject = "Error Resolution Notification";
                        $mail_cont = "<h1>$APP_inTimeSerial: Some errors have been fixed in your PhotoBooths</h1>";
                        $mail_cont.= $MAILNS_cont;
                        $mail_cont.= "<br/><p>You received this email because your email address is registered as the owner of a DC PhotoBooth</p>";


    //?                    include("../../common/APP_mail.php");
                        include(dirname(__FILE__) . '/../../common/APP_mail.php');
                    
                    }
            
//            APP_fesLog("eMail notification mail_email: $mail_email. mail_ret: $mail_retMsg");
//$fh = fopen("logAPP-email.dat", 'a');
//fwrite($fh, "eMail notification mail_email: $mail_email. mail_ret: $mail_retMsg\r");
//fclose($fh);
            
                    
                 }
                 $APP_BdD->CloseRs();
            }
        }
        
        
        
//20140421mails FINAL        
        
        
        
//20140331 INICI        
        //teniem el APP_common_checkAlerts abans de l?UPDARTE de App_boothAlert. Per això el PB seguia estant en estat == 1
        //cal actualitzar l'estat del booth, també actualitzarà $APP_common_badge
        include 'APP_common_checkAlerts.php';
        if($APP_common_error) return;

    //????    include("../../easyapns/src/php/APP_apns.php");
//20140422 ha d'estar abans!!!!!!!!!!        include(dirname(__FILE__) . '/../../easyapns/src/php/APP_apns.php');
       // $APP_nameBooth
        
//20140331 FINAL        
        
        
    }
?>
