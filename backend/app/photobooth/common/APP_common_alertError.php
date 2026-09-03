<?php
//creat el 23/05/2013
//quan entra un error amb PB_Error.php comprovarem si ja estava controlat
//si no estava controlat caldrà insertar-lo a la taula App_boothAlert
//$APP_common_alertError serà 51,52,53 o 54

//Aqui només insertem errors si calen, 

$APP_common_error = false;
$APP_common_errorToInfo = false;
if($APP_common_alertError){ 
//    if($APP_common_alertErrorKO){ //cal activar l'alerta si encara no ho està
        //comprovem si ja hi ha una activa SELECT `id`, `idBooth`, `typeAlert`, `when`, `estat` FROM `App_boothAlert` WHERE 1
        $sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = $APP_common_alertError AND estat<2;";
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
            echo "Error - Common alertError - code 02 $sql";
            $APP_common_error = true;
            return;
        }
        $calInsertar = true;
        $APP_common_errorToInfo = true;
        if($APP_BdD->FetchRs()){
            $calInsertar = false;
            $APP_common_errorToInfo = false;
        }
        $APP_BdD->CloseRs();
        if($calInsertar){
            $sql = "INSERT INTO App_boothAlert SET  idBooth = $APP_idBooth, typeAlert = $APP_common_alertError, estat = 0, `when`=$APP_inTimeSerial";
            $esOK = $APP_BdD->Execute($sql);
            if(!$esOK) {
                echo "Error - Common alertError - code 03 $sql.";
                $APP_common_error = true;
                return;

            }
            
            //actualitzarà l'estat del booth, també actualitzarà $APP_common_badge
            include 'APP_common_checkAlerts.php';
            if($APP_common_error) return;
            //generem la notificació
            
//20170220apns            
//            include(dirname(__FILE__) . "/../../easyapns/src/php/APP_apns.php");
//           // $APP_nameBooth
//            $APNS_MessageAdded = APNS_addAlertError($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge,$APP_common_alertError);

 
//20140421mails INICI        
            $MAILNS_cont = "";


            switch($APP_common_alertError){
                case 51:
                    $MAILNS_cont.= "<p>Printer error at $APP_nameBooth</p>";
                    $textError= "Printer error at $APP_nameBooth"; //20140701
                    break;
                case 52:
                    $MAILNS_cont.= "<p>Paper error at $APP_nameBooth</p>";
                    $textError= "Paper error at $APP_nameBooth";//20140701
                    break;
                case 53:
                    $MAILNS_cont.= "<p>I/O Board error at $APP_nameBooth</p>";
                    $textError= "I/O Board error at $APP_nameBooth";//20140701
                    break;
                case 54:
                   $MAILNS_cont.= "<p>Camera error at $APP_nameBooth</p>";
                   $textError= "Camera error at $APP_nameBooth";//20140701
                   break;
            }
 
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
//20140701                        $mail_subject = "Error Notification";
                        
//20150625location                        $mail_subject = "Error Notification: $textError";//20140701
                        $mail_subject = "Error Notification: $textError. Location name: $APP_locationBooth";//20150625location
                        
                        $mail_cont = "<h1>$APP_inTimeSerial: An error has been detected in one of your PhotoBooths</h1>";
                        $mail_cont.= $MAILNS_cont;
                        $mail_cont.= "<p>Location name: $APP_locationBooth</p>";//20150625location
                        
                        $mail_cont.= "<p>Photobooth ID: $APP_idBooth</p>";//20150629PBid
                        
                        
                        
                        $mail_cont.= "<br/><p>You received this email because your email address is registered as the owner of a DC PhotoBooth</p>";
                    
                    
//?                    include("../../common/APP_mail.php");
                    include(dirname(__FILE__) . '/../../common/APP_mail.php');
                    
                    error_log( "APP_common_alertError.php mail_retMsg: $mail_retMsg " );//20250205mail
                    }
            
//            APP_fesLog("eMail notification mail_email: $mail_email. mail_ret: $mail_retMsg");
//$fh = fopen("logAPP-email.dat", 'a');
//fwrite($fh, "eMail notification mail_email: $mail_email. mail_ret: $mail_retMsg\r");
//fclose($fh);
            
                    
                 }
                 $APP_BdD->CloseRs();
            }
        
//20140421mails FINAL        
            
        }
//    }
//    else{//mirem si cal desactivar l'alerta
//        $sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = $APP_common_alertError AND estat<2;";
//        $esOK = $APP_BdD->OpenRs($sql);
//        if(!$esOK){
//            echo "Error - Common alertError - code 05 $sql";
//            $APP_common_error = true;
//            return;
//        }
//        $calTreure = false;
//        if($APP_BdD->FetchRs()){
//            $calTreure = true;
//        }
//        $APP_BdD->CloseRs();
//        if($calTreure){
//            $sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = $APP_common_alertError;";
//            $esOK = $APP_BdD->Execute($sql);
//            if(!$esOK) {
//                echo "Error - Common alertError - code 06 $sql.";
//                $APP_common_error = true;
//                return;
//
//            }
//            //cal actualitzar l'estat del booth, també actualitzarà $APP_common_badge
//            include 'APP_common_checkAlerts.php';
//            if($APP_common_error) return;
////201305 INICI
//            include("../../easyapns/src/php/APP_apns.php");
//           // $APP_nameBooth
//            APNS_addOkError($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge);//,$APP_common_alertError);
////201305 FINAL
//
//        }
//    }
//        
    
    
} 

?>
