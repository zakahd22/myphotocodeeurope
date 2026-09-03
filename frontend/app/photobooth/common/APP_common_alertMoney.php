<?php
$APP_common_error = false;
if($APP_common_money){ 
    //cerquem umbral d'alerta SELECT `idBooth`, `typeAlert`, `value` FROM `App_boothAlertDef` WHERE 1
    $sql = "SELECT value FROM App_boothAlertDef WHERE idBooth = $APP_idBooth AND typeAlert = 12;";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        echo "Error - Common alertMoney - code 01 $sql";
        $APP_common_error = true;
        return;
    }
    $llindar = 0;
    if($APP_BdD->FetchRs()){
        $llindar = intval($APP_BdD->GetField(1));
    }
    $APP_BdD->CloseRs();
    if($llindar > 0){
        if($APP_common_money > $llindar){ //caldria activar l'alerta
            //comprovem si ja hi ha una activa SELECT `id`, `idBooth`, `typeAlert`, `when`, `estat` FROM `App_boothAlert` WHERE 1
            $sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = 12 AND estat<2;";
            $esOK = $APP_BdD->OpenRs($sql);
            if(!$esOK){
                echo "Error - Common alertMoney - code 02 $sql";
                $APP_common_error = true;
                return;
            }
            $calInsertar = true;
            if($APP_BdD->FetchRs()){
                $calInsertar = false;
            }
            $APP_BdD->CloseRs();
            if($calInsertar){
                $sql = "INSERT INTO App_boothAlert SET  idBooth = $APP_idBooth, typeAlert = 12, estat = 0, `when`=$APP_inTimeSerial";
                $esOK = $APP_BdD->Execute($sql);
                if(!$esOK) {
                    echo "Error - Common alertMoney - code 03 $sql.";
                    $APP_common_error = true;
                    return;

                }
                
//201305 INICI
//                include("../../easyapns/src/php/APP_apns.php");//201303
//                APNS_addAlertStock($idClient);//201303
//                //
//                //i canviem l'estat del booth
//                //SELECT `idBooth`, `estat`, `type`, `owner`, `name`, `obs`, `serialnumber`, `location`, `latitude`, `longitude`, `alertOffline`, `hS`, `mS`, `hE`, `mE`, `report` FROM `App_booths` WHERE 1
//                $sql = "UPDATE App_booths SET estat=1 WHERE idBooth = $APP_idBooth";
//                $esOK = $APP_BdD->Execute($sql);
//                if(!$esOK) {
//                    echo "Error - Common alertMoney - code 04 $sql.";
//                    $APP_common_error = true;
//                    return;
//
//                }
            
                //actualitzarà l'estat del booth, també iniciarà $APP_common_badge
                include 'APP_common_checkAlerts.php';
                if($APP_common_error) return;
                //generem la notificació
                
//20170220apns                
//                include(dirname(__FILE__) . "/../../easyapns/src/php/APP_apns.php");
//               // $APP_nameBooth
//                $APNS_MessageAdded = APNS_addAlertMoney($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge);

//201305 FINAL
                
                
 
//20140421mails INICI        
            //cal enviar un email a l'owner
            $sql = "SELECT name,`App_email` FROM rentals WHERE id=$APP_idRental; ";
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
//20140701                        $mail_subject = "Alert Detection Notification";// 
//20150625location                        $mail_subject = "Alert Detection Notification: Cash Box Full at $APP_nameBooth ";// //20140701 
                        $mail_subject = "Alert Detection Notification: Cash Box Full at $APP_nameBooth. Location name: $APP_locationBooth";//20150625location 
                        $mail_cont = "<h1>$APP_inTimeSerial: An alert has been detected in one of your PhotoBooths</h1>";
                        $mail_cont.= "<p>Cash Box Full at $APP_nameBooth</p>";
                        $mail_cont.= "<p>Location name: $APP_locationBooth</p>";//20150625location
                        $mail_cont.= "<p>Photobooth ID: $APP_idBooth</p>";//20150629PBid
                        $mail_cont.= "<br/><p>You received this email because your email address is registered as the owner of a DC PhotoBooth</p>";
    //?                    include("../../common/APP_mail.php");
                        include(dirname(__FILE__) . '/../../common/APP_mail.php');
                    }
                 }
                 $APP_BdD->CloseRs();
            }
//20140421mails FINAL        
                         
                
                
            }//end if($calInsertar)
        }
        else{//mirem si cal desactivar l'alerta
            $sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = 12 AND estat<2;";
            $esOK = $APP_BdD->OpenRs($sql);
            if(!$esOK){
                echo "Error - Common alertMoney - code 05 $sql";
                $APP_common_error = true;
                return;
            }
            $calTreure = false;
            if($APP_BdD->FetchRs()){
                $calTreure = true;
            }
            $APP_BdD->CloseRs();
            if($calTreure){
                $sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = 12;";
                $esOK = $APP_BdD->Execute($sql);
                if(!$esOK) {
                    echo "Error - Common alertMoney - code 06 $sql.";
                    $APP_common_error = true;
                    return;

                }
                //cal actualitzar l'estat del booth
                include 'APP_common_checkAlerts.php';
                if($APP_common_error) return;
//201305 INICI
//20170220apns
//            include(dirname(__FILE__) . "/../../easyapns/src/php/APP_apns.php");
//           // $APP_nameBooth
//            $APNS_MessageAdded = APNS_addOkMoney($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge);
//201305 FINAL

            
 
//20140421mails INICI        
            //cal enviar un email a l'owner
            $sql = "SELECT name,`App_email` FROM rentals WHERE id=$APP_idRental; ";
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
//20150625location                        $mail_subject = "Alert Resolution Notification";// 
                        $mail_subject = "Alert Resolution Notification. Location name: $APP_locationBooth";//20150625location
                        $mail_cont = "<h1>$APP_inTimeSerial: An alert has been solved in one of your PhotoBooths</h1>";
                        $mail_cont.= "<p>Cash Box Status OK at $APP_nameBooth</p>";
                        $mail_cont.= "<p>Location name: $APP_locationBooth</p>";//20150625location
                        $mail_cont.= "<p>Photobooth ID: $APP_idBooth</p>";//20150629PBid
                        $mail_cont.= "<br/><p>You received this email because your email address is registered as the owner of a DC PhotoBooth</p>";
    //?                    include("../../common/APP_mail.php");
                        include(dirname(__FILE__) . '/../../common/APP_mail.php');
                    }
                 }
                 $APP_BdD->CloseRs();
            }
//20140421mails FINAL        
                         
            
            
            
            }
        }
        
    
    }
    
} 

?>
