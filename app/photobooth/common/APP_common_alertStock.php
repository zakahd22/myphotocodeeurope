<?php
$APP_common_error = false;

APP_fesLogDebbug("APP_common_alertStock ($APP_idBooth) TRACE01 before if($ APP_common_stock): $APP_common_stock","logDebugAPP_common_alertStock201903");

//201903stock if($APP_common_stock){ 
if(intval($APP_common_stock) > 0){ //201903stock
    
    
 
APP_fesLogDebbug("APP_common_alertStock ($APP_idBooth) TRACE02 after if($ APP_common_stock): $APP_common_stock","logDebugAPP_common_alertStock201903");

   
    
    //cerquem umbral d'alerta SELECT `idBooth`, `typeAlert`, `value` FROM `App_boothAlertDef` WHERE 1
    $sql = "SELECT value FROM App_boothAlertDef WHERE idBooth = $APP_idBooth AND typeAlert = 11;";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        echo "Error - Common alertStock - code 01 $sql";
        $APP_common_error = true;
        return;
    }
    $llindar = 0;
    if($APP_BdD->FetchRs()){
        $llindar = intval($APP_BdD->GetField(1));
    }
    $APP_BdD->CloseRs();
    
     
APP_fesLogDebbug("APP_common_alertStock TRACE03 llindar: $llindar","logDebugAPP_common_alertStock201903");


    
    if($llindar > 0){
        if($APP_common_stock < $llindar){ //cal activar l'alerta
            //comprovem si ja hi ha una activa SELECT `id`, `idBooth`, `typeAlert`, `when`, `estat` FROM `App_boothAlert` WHERE 1
            $sql = "SELECT id, `when` FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = 11 AND estat<2;";
//            echo $sql;
            $esOK = $APP_BdD->OpenRs($sql);
            if(!$esOK){
                echo "Error - Common alertStock - code 02 $sql";
                $APP_common_error = true;
                return;
            }
            $reenviamentSetmanal = 0; 
            $calInsertar = true; 
            if($APP_BdD->FetchRs()){
                $calInsertar = false;
                //Mirem si fa una setmana 20220117 20-D-11 Film alert AFEGIM UN AVÍS SETMANAL
                $dataUltimEmail = $APP_BdD->GetField(2);
                $alertId = $APP_BdD->GetField(1);
                $datetime1 = new DateTime($dataUltimEmail); // date to check
                $datetime2 = new DateTime(); // now
                $interval = $datetime1->diff($datetime2);
                $dateDiff = $interval->format('%r%a'); // %r returns the sign; if date is in the past, returns a positive number
//echo "Date older than $dateDiff days\n";
                if ($dateDiff >= 7) {

                    $reenviamentSetmanal = 1; 
//                        echo "Date older than  days\n";
                    //Aqui fem UPDATE de la data per insertar avui NOW() i que no torni a enviar fins passada una setmana
                    $sql = "UPDATE App_boothAlert SET  `when`= NOW() WHERE id = $alertId";
                    $esOK = $APP_BdD->Execute($sql);
                    if(!$esOK) {
                        echo "Error - Common alertStock - code 03 $sql. UPDATE when";
                        $APP_common_error = true;
                        return;

                }
            }
                
                
            }
            $APP_BdD->CloseRs();
            
            
            

            
            
            
            
            if($calInsertar || $reenviamentSetmanal){ //si cal insertar envia. Si no, no envia alerta. 20220117 20-D-11 Film alert AFEGIM UN AVÍS SETMANAL fins que canviïn el rotllo de film, moment en que les alarmes es desactiven.
                if($calInsertar){
                    $sql = "INSERT INTO App_boothAlert SET  idBooth = $APP_idBooth, typeAlert = 11, estat = 0, `when`=$APP_inTimeSerial";
                    $esOK = $APP_BdD->Execute($sql);
                    if(!$esOK) {
                        echo "Error - Common alertStock - code 03 $sql.";
                        $APP_common_error = true;
                        return;

                    }
                
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
//                    echo "Error - Common alertStock - code 04 $sql.";
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
//                $APNS_MessageAdded = APNS_addAlertStock($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge);
//201305 FINAL
               
 
//20140421mails INICI        
            //cal enviar un email a l'owner
            $sql = "SELECT name,`App_email` FROM rentals WHERE id=$APP_idRental;";
            $esOK = $APP_BdD->OpenRs($sql);
            if($esOK){
                 if($APP_BdD->FetchRs()){
                    $mail_nom =  $APP_BdD->GetField(1);
                    $mail_email =  $APP_BdD->GetField(2);
                    
                    if($mail_email){
                        //20220107 20-D-11 Film alert INICI
                        $noSabemPrinter = 0;
                        $boothType = "";
                        $printerType = "";
                        $serialNumber = "";
                        $serialNumberCurt = 0;
                        //if($APP_idBooth==8122){ //TODO: comentar quan haguem testejat
                            /**
                             * 1.- SI SABEM QUINA IMPRESSORA TÉ: LINK AL PAPER
                             */
                            //si està entrat el serialnumber a podem saber quina impresora té i per tant quin paper fa servir
                            $sql = "SELECT type FROM `CLD_components` WHERE  type in (16,28,29,30,32,38,39)
                                    ORDER BY `CLD_components`.`data_entrada`  DESC limit 1;"; //DESC perquè volem saber la última que se li ha afegit
                            $esOK = $APP_BdD->OpenRs($sql);
                            if($esOK){
                                     if($APP_BdD->FetchRs()){
                                     $printerType =  $APP_BdD->GetField(1);
                                     //si es 30 RX1 DNP, o 39 ET-3700 no enviarem link
                                     }
                                }     
//$printerType = "32"; //TODO: comentar quan haguem testejat
                            switch($printerType){
                                                                         
                                    //FILM: CK9046DC
                                        case "16":   //9550
                                        case "32":   //9810
                                       
                                    
                                        //FILM: CK9046DC
                                      
                                            $linkPaperHTML .= "<br/><p>For the <b>Mitsubishi 9550 and 9810</b> printer place your order of film here: <a href='https://photoboothparts.com/278-ck9046dc'>https://photoboothparts.com/278-ck9046dc   <img id='bigpic' itemprop='image' src='https://eu.photoboothparts.com/131-large_default/ck9046dc-01-film-box-of-1-roll-ck9046-dc-600-vends-1200-strips.jpg' width='150' height='150'></a></p>";                                        
                                        break;
                                    
                                        //FILM: FL68
                                        case "28":   //D80
                                        case "38":   //D90
                                            //FILM: FL68
                                        
                                            $linkPaperHTML = "<br/><p>For the <b>Mitsubishi D80 and D90</b> printer place your order of film here: <a href='https://photoboothparts.com/281-fl68'>https://photoboothparts.com/281-fl68   <img id='bigpic' itemprop='image' src='https://eu.photoboothparts.com/424-large_default/fl68-01-film-box-of-2-rolls-fl68-860-vends-1720-strips-.jpg' width='150' height='150'></a></p>";
                                        break;
                                    
                                    //FILM: PO68
                                        case "29":  //DS620 
                                        //FILM: PO68
                                    
                                    
                                            $linkPaperHTML .= "<br/><p>For the <b>DNP DS620</b> printer place your order of film here: <a href='https://photoboothparts.com/283-po68'>https://photoboothparts.com/283-po68 <img id='bigpic' itemprop='image' src='https://photoboothparts.com/421-large_default/po68-01-film-box-of-2-rolls-800-vends-1600-strips.jpg' width='150' height='150'></a></p>";                                        
                                        break;
                                    
                                                                          
                                                                        

                                    default:
                                        $linkPaperHTML = ""; //Sense link, no sabem segur quina printer o paper
                                            $noSabemPrinter = 1;
                                        break;

                                }


                            /**
                             * 2.- SI NO SABEM QUINA IMPRESSORA TÉ: 
                             *         A: Si el serialnumber es anterior  a 00XX000XX1291 no ens arrisquem, sense link
                             *         B: Si el serialnumber es posterior a 00XX000XX1291 POSEM LINK A CK9046DC i FL68
                             */
                            //Segons CLD_idType podem saber quina impresora té i per tant quin paper fa servir
                            $sqlType = "SELECT `CLD_idType`, REPLACE( serialnumber, LEFT( serialnumber, 9 ) , '' ) AS serialnumbercurt, serialnumber FROM App_booths WHERE idBooth = $APP_idBooth;";
                            $esOKType = $APP_BdD->OpenRs($sqlType);
                            if($esOKType){
                                 if($APP_BdD->FetchRs()){
                                    $boothType =  $APP_BdD->GetField(1);
                                    $serialNumberCurt = $APP_BdD->GetField(2);
                                    $serialNumber = $APP_BdD->GetField(3);
                                 }
                            }           
                            if($noSabemPrinter==1){
                                //TODO: treure quan ho tinguis acabat
                                $linkPaperHTML = ""; //Sense link, no sabem segur quina printer o paper
                                    //
                                    //TODO: PER ACABAR, POTSER DESCARTAREM AQUEST CODI SI ENTREM TOTES LES IMPRESSORES A LA BD 
                                    //mantenim switch per descartar boothType noves com selfiePhotoMask o antigues que no tenen paper al web o no sabem segur que tindrà
//                                    switch($boothType){
//
//                                        //FILM: CK9046DC (provablement)
//                                        case "2":   //C     Mega Strip 
//                                        case "3":   //B	Wall Strip 
//                                        case "4":   //E	Party'n'Go 
//                                        case "7":   //D	Mega Combo
//                                        case "12":  //H	I-GO                                    
//                                        case "21":  //H     Sweet I-GO 
//                                        case "22":  //H	I-GO Kids 
//                                        case "35":  //F	Eclipse  
//                                    
//
//                                        //FILM: CK9046DC, CK8000FL4PDC o FL68  (provablement)
//                                        case "6":   //F	NG Crystal
//                                        case "8":   //F	NG Panthercase 
//                                        case "9":   //F	NG Video&Net Blue v2.0
//                                        case "10":  //F	NG Video&Net Black v2.0
//                                        case "11":  //F	NG Video&Net White v2.0
//                                        case "24":  //Q	NG Owl                                    
//                                        case "25":  //Q	NG Dolphin
//                                        case "26":  //Q	NG Diamond  
//                                            
//                                        //FILM: FL68 (provablement)
//                                        case "47":  //W	Let's Print
//                                        case "48":  //V	Smile'n'Stick         
//                                            
//                                        //FILM: CK9046DC o FL68  (provablement)
//                                        case "1":   //A	Strip
//                                        case "23":  //H	Mini I-GO
//                                        case "34":  //U	Nexus Strip
//                                        case "36":  //F	Panther Revolution
//                                        case "37":  //F	NG  Panther v4
//                                        case "38":  //F	NG  Panther v5                                  
//                                        case "39":  //F	NG  Panther v6
//                                        case "40":  //F	D'light                                         
//                                        case "41":  //F	Duplo 
//                                        case "42":  //F	Panther Cube
//                                        case "43":  //F	Upgrade Kit  
//                                        case "46":  //F	NG  Panther v7    
//                                        case "49":  //F	Star Light                                        
//                                            
//                                            if($serialNumberCurt>1291){ //era moderna totes porten printer 9550, 9810, D80 o D90: FILM CK9046DC o FL68
//                                                //FILM: CK9046DC //https://photoboothparts.com/278-ck9046dc
//                                               // $linkPaperHTML = '<p><img id="bigpic" itemprop="image" src="https://eu.photoboothparts.com/131-large_default/ck9046dc-01-film-box-of-1-roll-ck9046-dc-600-vends-1200-strips.jpg" title="Film compatible with Mitsubishi 9550 and 9810 printer model. WARNING!! Make sure this is the printer in your Photobooth to av" alt="Film compatible with Mitsubishi 9550 and 9810 printer model." width="150" height="150"></p>';
//
//                                                $linkPaperHTML .= "<br/><p>For the <b>Mitsubishi 9550 and 9810</b> printer place your order of film here: <a href='https://photoboothparts.com/278-ck9046dc'>https://photoboothparts.com/278-ck9046dc <img id='bigpic' itemprop='image' src='https://eu.photoboothparts.com/131-large_default/ck9046dc-01-film-box-of-1-roll-ck9046-dc-600-vends-1200-strips.jpg' title='Film compatible with Mitsubishi 9550 and 9810 printer model. WARNING!! Make sure this is the printer in your Photobooth to av' alt='Film compatible with Mitsubishi 9550 and 9810 printer model.' width='150' height='150'></a></p>";                                       
//                                                //FILM: FL68 //https://photoboothparts.com/281-fl68
//                                                //$linkPaperHTML = '<br/><p><img id="bigpic" itemprop="image" src="https://eu.photoboothparts.com/424-large_default/fl68-01-film-box-of-2-rolls-fl68-860-vends-1720-strips-.jpg" title="Film compatible with Mitsubishi D80DC and D90DC printer model. WARNING!! Make sure this is the printer in your Photobooth to " alt="Film compatible with Mitsubishi D80DC and D90DC printer model." width="150" height="150"></p>';
//                                                $linkPaperHTML .= "<br/><p>For the <b>Mitsubishi D80 and D90</b> printer place your order of film here: <a href='https://photoboothparts.com/281-fl68'>https://photoboothparts.com/281-fl68 <img id='bigpic' itemprop='image' src='https://eu.photoboothparts.com/424-large_default/fl68-01-film-box-of-2-rolls-fl68-860-vends-1720-strips-.jpg' title='Film compatible with Mitsubishi D80DC and D90DC printer model. WARNING!! Make sure this is the printer in your Photobooth to ' alt='Film compatible with Mitsubishi D80DC and D90DC printer model.' width='150' height='150'><img id='bigpic' itemprop='image' src='https://eu.photoboothparts.com/424-large_default/fl68-01-film-box-of-2-rolls-fl68-860-vends-1720-strips-.jpg' title='Film compatible with Mitsubishi D80DC and D90DC printer model. WARNING!! Make sure this is the printer in your Photobooth to ' alt='Film compatible with Mitsubishi D80DC and D90DC printer model.' width='150' height='150'></a></p>";
//                                                
//                                            }
//                                            
//                                            break;    
//
//                                           
//
//                                        //FILM: PO68 (provablement)
//                                        case "33":  //S	Pocket  
//
//                                            //FILM: PO68
//                                            $linkPaperHTML = ""; //no arrisquem, hauria de ser antiga                                   
//                                            break;
//
//
//                                        
//
//                                       
//
//                                          
//                                        default:
//                                            $linkPaperHTML = ""; //Sense link, no sabem segur quina printer o paper
//                                            break;
//
//                                    }
                                
                            }        
                                    
                            
                                   
                      //  }  //fi if 8122      
                        //20220107 20-D-11 Film alert FINAL
                        
                        
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
                        
//20150625location                        $mail_subject = "Alert Detection Notification: Run Out of Film at $APP_nameBooth";// //20140701
                        $mail_subject = "Alert Detection Notification: RUN OUT OF FILM at $APP_nameBooth. Location name: $APP_locationBooth";//20150625location
                        
                        
                        $mail_cont = "<h1>$APP_inTimeSerial: An alert has been detected in one of your PhotoBooths</h1>";
                        $mail_cont.= "<p>Run Out of Film at $APP_nameBooth</p>";
                        $mail_cont.= "<p>Location name: $APP_locationBooth</p>";//20150625location
                        $mail_cont.= "<p>Current STOCK in PRINTER: ".$stock." prints</p>";
                        $mail_cont.= "<p>Photobooth ID: $APP_idBooth // S/N: $serialNumber</p>";//20150629PBid
                        $mail_cont.= $linkPaperHTML; //20220107 20-D-11 Film alert
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
        else{//mirem si cal desactivar l'alerta
            $sql = "SELECT id FROM App_boothAlert WHERE idBooth = $APP_idBooth AND typeAlert = 11 AND estat<2;";
            $esOK = $APP_BdD->OpenRs($sql);
            if(!$esOK){
                echo "Error - Common alertStock - code 05 $sql";
                $APP_common_error = true;
                return;
            }
            $calTreure = false;
            if($APP_BdD->FetchRs()){
                $calTreure = true;
            }
            $APP_BdD->CloseRs();
            if($calTreure){
                $sql = "UPDATE App_boothAlert SET estat=2 WHERE idBooth = $APP_idBooth AND typeAlert = 11;";
                $esOK = $APP_BdD->Execute($sql);
                if(!$esOK) {
                    echo "Error - Common alertStock - code 06 $sql.";
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
//           $APNS_MessageAdded = APNS_addOkStock($APP_idRental,$idBooth,$APP_nameBooth,$APP_common_badge);
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
                        $mail_cont.= "<p>Film Stock OK at $APP_nameBooth</p>";
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
