<?php
$APP_common_error = false;
//com que acabem de rebre info, cal desactivar qualsevol alerta offline del booth
$sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = 1 AND estat<2;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    echo "Error - Common alertOffline - code 01 $sql";
    $APP_common_error = true;
    return;
}
$calTreure = false;
if($APP_BdD->FetchRs()){
    $calTreure = true;
}
$APP_BdD->CloseRs();
    
    

if($calTreure){

    $sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = 1;";
//APP_fesLog("TRACE20140615 alertOfflineToOk - calTreure sql: $sql");//20160615trace  
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        echo "Error - Common alertOffline - code 02 $sql.";
        $APP_common_error = true;
        return;

    }
    
//APP_fesLog("TRACE20140615 alertOfflineToOk - Abans APP_common_checkAlerts");//20160615trace   
    
    
    //cal actualitzar l'estat del booth
//20140616    include 'APP_common_checkAlerts.php';
    
//APP_fesLog("TRACE20140615 alertOfflineToOk - Despres APP_common_checkAlerts. APP_common_error: --$APP_common_error--");//20160615trace   
    
    if($APP_common_error) return;
    
    
//APP_fesLog("TRACE20140615 alertOfflineToOk - Abans de include " . dirname(__FILE__) . '/../../easyapns/src/php/APP_apns.php');//20160615trace   
    
//20140616 INICI
//ob_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//
////$filename = dirname(__FILE__) . '/../../easyapns/src/php/APP_apns.php';
////
////if((@include $filename) === false)
////{
////APP_fesLog("TRACE20140615 alertOfflineToOk - Erros in include APP_apns $filename");   
////}
//

//20140616 FINAL


//201305 INICI
//20170220apns    include_once(dirname(__FILE__) . '/../../easyapns/src/php/APP_apns.php'); //20160616
//20160615            include(dirname(__FILE__) . "/../../easyapns/src/php/APP_apns.php");
    
//APP_fesLog("TRACE20140615 alertOfflineToOk - Despres de include APP_apns");//20160615trace   
    

    //cal actualitzar l'estat del booth
    include_once 'APP_common_checkAlerts.php';//20140616


           // $APP_nameBooth
//20140615!!           $APNS_MessageAdded = APNS_addOkOffline($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge);
//20170220apns           $APNS_MessageAdded = APNS_addOkOffline($APP_idRental,$APP_nameBooth,$APP_nameBooth,$APP_common_badge);//20140615!! 
//201305 FINAL
   
    
//APP_fesLog("TRACE20140615 alertOfflineToOk - Despres de APNS_addOkOffline");//20160615trace   
    
//20140616 INICI
//APP_fesLog("TRACE20140615 ob " . ob_get_contents());
//    ob_end_clean();
//error_reporting(0);
//ini_set('display_errors', 0);

//20140616 FINAL
       
 
//20140421mails INICI        
            //cal enviar un email a l'owner
            //$sql = "SELECT name,`App_email` FROM rentals WHERE id=$APP_idRental; "; 
            
            /***
             * 20220426
             * Afegim més informació del Booth
             */
            $sql = "SELECT T3.name, T3.`App_email`, T2.name, T1.serialnumber, T1.version, T5.rand_string
       FROM App_booths T1 
       
       LEFT  JOIN CLD_boothTypes T2 ON T1.CLD_idType = T2.id 
       LEFT  JOIN rentals T3 ON T1.owner = T3.id 
       LEFT  JOIN App_boothDongle T4 ON T1.idBooth = T4.idBooth AND datetimeF IS NULL 
       LEFT  JOIN booths T5 ON T4.idDongle = T5.id 
       WHERE T1.idBooth = $APP_idBooth ";

            
            
            
            
            
            
            $esOK = $APP_BdD->OpenRs($sql);
            if($esOK){
                 if($APP_BdD->FetchRs()){
                    $mail_nom =  $APP_BdD->GetField(1);
                    $mail_email =  $APP_BdD->GetField(2);
                    /***
                    * 20220426
                    * Afegim més informació del Booth
                    */                                    
                   // $boothName = $APP_BdD->GetField(3); 
                    //$boothLastConnZone = $APP_BdD->myDateTimeSerial($APP_BdD->GetFieldDateTime(4));        
                    //$boothLocation = $APP_BdD->GetField(5);
                    $boothType = $APP_BdD->GetField(3);
                    $boothSnumber = $APP_BdD->GetField(4);  
                    $boothVersion = $APP_BdD->GetField(5);
                    
                    
                    if(is_null($APP_BdD->GetField(6))){
                       $htmlRandString="";  
                    }else{
                        $htmlRandString="<tr>
                                        <td style='padding-right:10px;'><b>STRING:</b></td>
                                        <td>".$APP_BdD->GetField(6)."</td>
                                      </tr>";  
                    }
                    
                    
    
//APP_fesLog("TRACE20140615 alertOfflineToOk - Despres de SELECT FROM rentals mail_email: $mail_email");//20160615trace   
    
                    
                    
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
//20150625location                        $mail_subject = "Alert Resolution Notification"; 
                        $mail_subject = "Alert Resolution Notification. Location name: $APP_locationBooth";//20150625location
                       /***
                        * 20220426
                        * Afegim més informació del Booth
                        */  
//                        $mail_cont = "<h1>$APP_inTimeSerial: An alert has been solved in one of your PhotoBooths</h1>";
//                        $mail_cont.= "<p>$APP_nameBooth is now ONLINE</p>";
//                        $mail_cont.= "<p>Location name: $APP_locationBooth</p>";//20150625location
//                        $mail_cont.= "<p>Photobooth ID: $APP_idBooth</p>";//20150629PBid
                        $mail_cont.="<h2>Your Photobooth is ONLINE</h2>";
                        $mail_cont.="
                            <table border='0' cellpadding='1' cellspacing='0' >
                               <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH NAME:</b></td>
                                <td>$APP_nameBooth</td>
                              </tr>
                              <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH MODEL:</b></td>
                                <td>$boothType</td>
                              </tr>";
                        $mail_cont.="
                              <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH S/N:</b></td>
                                <td>$boothSnumber</td>
                              </tr>";
                        $mail_cont.= $htmlRandString;
                        $mail_cont.="
                              <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH LOCATION:</b></td>
                                <td>$APP_locationBooth</td>
                              </tr>
                              <tr>
                                <td style='padding-right:10px;'>Id:</td>
                                <td>$APP_idBooth</td>
                              </tr>";
                         $mail_cont.="
                              <tr>
                                <td style='padding-right:10px;'>Owner:</td>
                                <td>$mail_nom</td>
                              </tr>
                              <tr>
                                <td style='padding-right:10px;'>Version:</td>
                                <td>$boothVersion</td>
                              </tr>
                            </table>";
                        $mail_cont.= "<br/><p>You received this email because your email address is registered as the owner of a DC PhotoBooth</p>";
    //?                    include("../../common/APP_mail.php");
                        include(dirname(__FILE__) . '/../../common/APP_mail.php');
                    }
                 }
                 $APP_BdD->CloseRs();
            }
//20140421mails FINAL        
                         
          
}



?>
