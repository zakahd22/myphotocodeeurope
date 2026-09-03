<?php
/******
 * 2022-05-16
 * Per a Britta =>3.2 
 * Ampliem info dels errors per email
 * #tractamentErrorsPartida 
 */
require("../common/APP_BdD.php");

require("../common/APP_common.php");

//function sort_by_when ($a, $b) {
//    return $a['when'] - $b['when'];
//}



/***
 * mirem si hi ha algún error pendent de comunicar
 * Seleccionem per excloure els PB que puguin estar en un cicle Oops ara mateix. 
 * No volem comunicar-ho per email fins que acabi
 * el PB no té cap error per enviar de menys d'una hora
 */


$sql = "SELECT idBooth FROM App_infoDeviceMgt aidm 
WHERE sent=0 AND  aidm.`db_time` > DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY aidm.`when` ASC";
   
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        echo "Error - CronDeviceMgtv  1rst Select - code 00 $sql";
        $APP_common_error = true;
        return;
    }
    
    
 
    $excludeBoothsRecents = "";
    $i=0;
    while($APP_BdD->FetchArray()){
        $idBooth = $APP_BdD->GetArrayField("idBooth"); 
        if($i) $excludeBoothsRecents .= " ,";
        $excludeBoothsRecents .= $idBooth;
        $i++;
    }
    $APP_BdD->CloseRs();
    
    if($i) $excludeBoothsRecentsSql = " AND aidm.idBooth NOT IN ($excludeBoothsRecents) ";
  
/***
 * Select de error devices de fa més d'una hora, no enviats, 
 * però que per aquell PB no n'hi hagi cap més per enviar de menys d'una hora (esperem a que acabi i ja enviarem)
 * 
 */
    $sql = "SELECT aidm.idDM, aidm.when, aidm.idBooth, aidm.idDongle, aidm.device, aidm.model, aidm.attempts, aidm.resp, aidm.action, aidm.ok, aidm.sent, aidm.pbs_time , aidm.db_time , ab.name as pbName, ab.serialnumber, ab.location, r.id AS ownerId, r.name as ownerName, r.App_email, b.rand_string, cbt.name as typeName, ab.version, DATE_SUB(NOW(), INTERVAL 1 HOUR) as faUnaHora FROM App_infoDeviceMgt aidm 
LEFT JOIN App_booths ab ON ab.idBooth=aidm.idBooth
LEFT JOIN rentals r ON ab.owner=r.id
LEFT JOIN booths b ON b.id=aidm.idDongle
LEFT JOIN CLD_boothTypes cbt ON ab.CLD_idType = cbt.id 
WHERE sent=0 AND  aidm.`db_time` < DATE_SUB(NOW(), INTERVAL 1 HOUR)  ORDER BY aidm.`when` ASC";
print $sql;  
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        echo "Error - CronDeviceMgtv  2ond Select - code 00 $sql";
        $APP_common_error = true;
        return;
    }
    
    
 
    $idDM_In = " ( ";
    $iiin = 0;
    while($APP_BdD->FetchArray()){
        $idBooth =                                                  $APP_BdD->GetArrayField("idBooth"); 
        $device =                                                   $APP_BdD->GetArrayField("device");
        $idDM =                                                     $APP_BdD->GetArrayField("idDM");
        $arrayErrors[$idBooth][$device][$idDM]["idDM"] =            $idDM;
        if($iiin) $idDM_In .= ", ";
        $idDM_In .= "$idDM";
        $iiin++;
        $arrayErrors[$idBooth][$device][$idDM]["when"] =            $APP_BdD->GetArrayField("when");         
        $arrayErrors[$idBooth][$device][$idDM]["idBooth"] =         $idBooth; 
        $arrayErrors[$idBooth][$device][$idDM]["idDongle"] =        $APP_BdD->GetArrayField("idDongle"); 
        $arrayErrors[$idBooth][$device][$idDM]["device"] =          $device; 
        $arrayErrors[$idBooth][$device][$idDM]["model"] =           $model;
        $arrayErrors[$idBooth][$device][$idDM]["error"] =           $error;
        $arrayErrors[$idBooth][$device][$idDM]["attempts"] =        $APP_BdD->GetArrayField("attempts"); 
        $arrayErrors[$idBooth][$device][$idDM]["resp"] =            $APP_BdD->GetArrayField("resp"); 
        $arrayErrors[$idBooth][$device][$idDM]["action"] =          $APP_BdD->GetArrayField("action"); 
        $arrayErrors[$idBooth][$device][$idDM]["ok"] =              $APP_BdD->GetArrayField("ok"); 
        $arrayErrors[$idBooth][$device][$idDM]["sent"] =            $APP_BdD->GetArrayField("sent"); 
        $arrayErrors[$idBooth][$device][$idDM]["pbs_time"] =        $APP_BdD->GetArrayField("pbs_time"); 
        $arrayErrors[$idBooth][$device][$idDM]["db_time"] =         $APP_BdD->GetArrayField("db_time");
        $arrayErrors[$idBooth][$device][$idDM]["pbName"] =          $APP_BdD->GetArrayField("pbName");  
        $arrayErrors[$idBooth][$device][$idDM]["ownerName"] =       $APP_BdD->GetArrayField("ownerName"); 
        $arrayErrors[$idBooth][$device][$idDM]["ownerId"] =         $APP_BdD->GetArrayField("ownerId"); 
        $arrayErrors[$idBooth][$device][$idDM]["serialnumber"] =    $APP_BdD->GetArrayField("serialnumber"); 
        $arrayErrors[$idBooth][$device][$idDM]["location"] =        $APP_BdD->GetArrayField("location"); 
        $arrayErrors[$idBooth][$device][$idDM]["App_email"] =       $APP_BdD->GetArrayField("App_email"); 
        $arrayErrors[$idBooth][$device][$idDM]["rand_string"] =     $APP_BdD->GetArrayField("rand_string"); 
        $arrayErrors[$idBooth][$device][$idDM]["faUnaHora"] =       $APP_BdD->GetArrayField("faUnaHora"); 
        $arrayErrors[$idBooth][$device][$idDM]["typeName"] =        $APP_BdD->GetArrayField("typeName");
        $arrayErrors[$idBooth][$device][$idDM]["version"] =         $APP_BdD->GetArrayField("version");  
        
        
        
        
        
        
        
        
        
    }
    $idDM_In .= ") ";
    $APP_BdD->CloseRs();
    
// print "<pre>";   
// print_r($arrayErrors);






//print "<pre>";   

 $mail_cont = "<h1 style='font-size:16px;'>Device Alerts have been detected on some PhotoBooths</h1>";

foreach($arrayErrors as $idBootKey => $pbErrorsValue ){

    $htmlDevices = Array();
    foreach ($pbErrorsValue as $deviceKey =>  $deviceErrorsValue){
   
    
    
    usort($deviceErrorsValue, 'sort_by_when');
    

        $i=0;        
        foreach ($deviceErrorsValue as $deviceLineKey =>  $deviceLineValue){
                       
            $APP_araTimeSerial =   $deviceLineValue['when'];
            $pbName =              $deviceLineValue['pbName'];
            $typeName =            $deviceLineValue['typeName'];
            $serialnumber = $deviceLineValue['serialnumber'];
            $location = $deviceLineValue['location'];
            $idBooth = $deviceLineValue['idBooth'];
            $string = $deviceLineValue['rand_string'];
            $ownerName = $deviceLineValue['ownerName'];
            $pbVersion = $deviceLineValue['version'];
            $action = $deviceLineValue['action'];  
            $device = $deviceLineValue['device'];  
            $model = $deviceLineValue['model'];
            $errorCode = $deviceLineValue['resp']; //l'error ve inclós al parametre resp. Separat per | detalla la resposta de cada Attempt
            $attempts = $deviceLineValue['attempts']; 
            $resp = $deviceLineValue['resp']; 
            $ok = $deviceLineValue['ok']; 


            $sql = "SELECT * FROM CLD_Contactes 
            WHERE rental_id='".$deviceLineValue['ownerId']."'";
   
            $esOK = $APP_BdD->OpenRs($sql);
            if(!$esOK){
                echo "Error - Common alertErrorToOk - code 00 $sql";
                $APP_common_error = true;
                return;
            }

            $i=0;
            $contactsTxt = "";
            while($APP_BdD->FetchArray()){                
                
                //$contactsTxt .= "<tr style='border: 2px solid #000;' ><td colspan='6'>".$APP_BdD->GetArrayField("name")." ".$APP_BdD->GetArrayField("surnames").", ".$APP_BdD->GetArrayField("carrec")." </td></tr>";
                $contactsTxt .= "<tr style='border: 2px solid #000;' ><td><b>Email:</b></td><td colspan='3'> ".$APP_BdD->GetArrayField("email")." <td><b>Phone:</b></td><td> ".$APP_BdD->GetArrayField("phone")." (".$APP_BdD->GetArrayField("city").")</td></tr>";
                
            }
            $APP_BdD->CloseRs();
             
            switch ($action) {
               case 0:
                   $actionTxt = "Retry";
                   $backgroundLine="background-color: #f2b3aa;";  
                   $backgroundLineRec="background-color: #daf5e1;"; 
                   break;
               case 1:
                   $actionTxt = "FastReStart";
                   $backgroundLine="background-color: #f5a398;"; 
                   $backgroundLineRec="background-color: #b5e8c3;"; 
                   break;
               case 2:
                   $actionTxt = "Restart";
                   $backgroundLine="background-color: #f2b3aa;";  
                   $backgroundLineRec="background-color: #daf5e1;"; 
                   break;
               case 3:
                   $actionTxt = "Reboot";
                   $backgroundLine="background-color: #f5a398;"; 
                   $backgroundLineRec="background-color: #b5e8c3;"; 
                   break;
               default:
                   $actionTxt = "Unknown";
                   $backgroundLine="background-color: #f5a398;"; 
                   $backgroundLineRec="background-color: #b5e8c3;"; 
            }
            switch ($device) {
                 case 0:
                    $deviceTxt = "All";
                    $actionTxt = "Play";
                    $backgroundLine="background-color: #68d485;";
                    break;
                case 1:
                    $deviceTxt = "Control Board";
                     
                    break;
                case 2:
                    $deviceTxt = "Camera";
                    
                    break;
                case 3:
                    $deviceTxt = "Printer";
                     
                    break;
                default:
                    $deviceTxt = "Unknown";
                    
            }
           

            $recoveredTxt = "KO";
             if($ok){
                 $recoveredTxt = "OK";
                 $backgroundLine=$backgroundLineRec;

                 
             }
            
             if($attempts){
                 $attemptsTxt = "(x".$attempts." times)";
             }else{
                 $attemptsTxt = "";
             }
            
           // $htmlDevices[$device][$action] .= "<tr style='".$backgroundLine." border: 1px solid #fff;'><td style='border: 1px solid #fff;'>".$APP_araTimeSerial."</td><td style='border: 1px solid #fff;'> ".$deviceTxt." </td><td style='border: 1px solid #fff;'> ".$model." </td><td style='border: 1px solid #fff;'> ".$errorCode." </td><td style='border: 1px solid #fff;'> (x".$attempts." times)</td>"."<td style='border: 1px solid #fff;'>".$actionTxt."</td><td style='border: 1px solid #fff;'>".$recoveredTxt." </td></tr>";
                        $htmlDevices[$APP_araTimeSerial] .= "<tr style='".$backgroundLine." border: 1px solid #fff;'><td style='border: 1px solid #fff;'>".$APP_araTimeSerial."</td><td style='border: 1px solid #fff;'> ".$deviceTxt." </td><td style='border: 1px solid #fff;'> ".$model." </td><td style='border: 1px solid #fff;'> ".$errorCode." </td><td style='border: 1px solid #fff;'>".$attemptsTxt."</td>"."<td style='border: 1px solid #fff;'>".$actionTxt."</td><td style='border: 1px solid #fff;'>".$recoveredTxt." </td></tr>";

            $i++;
        }
    
    }
// print "htmlDevices";   
//  print_r($htmlDevices);
 
 //TODO: Tot això dins del foreach de dalt
    
       $mail_email = "eloi@dc-image.com";         
       $mail_copia = "dennis@dc-image.com";     
                    
                 
$mail_replayto = "main@dc-image.com";
//De moment no enviarem copia a main  
//    $mail_copia = "main@dc-image.com";
//    
   
//    $mail_copia = "";

                        $mail_remitent = "main@dc-image.com";//20150626
                        $mail_nomremitent = "DC Alerts Platform";

                        $mail_copia1 = "";
                        $mail_copianom1 = "";
                        $mail_copia2 = "";
                        $mail_copianom2 = "";
                    
                        $mail_subject = "Device Management Alert."; //20150625location
                        
                        
                       
                        //$mail_cont.="<h2>PhotoBooth Info</h2>";
                        $mail_cont.="
                           
                            <table border='0' cellpadding='5' cellspacing='0'  style='background-color: #e8f7ff; width: 800px;'>
                               <tr>
                                <td style='width:50px;'><b>Name:</b></td>
                                <td>$pbName</td>
                                <td  style=''><b>Model:</b></td>
                                <td>$typeName</td>  
                                <td  style=''><b>Version:</b></td>
                                <td>$pbVersion</td>
                              </tr>
                             ";
                        $mail_cont.="
                              <tr>
                                <td  style='width:50px;'><b>S/N:</b></td>
                                <td>$serialnumber - $string - $idBooth</td>
                                <td  style=''><b>Location:</b></td>
                                <td>$location</td>
                                <td  style=''><b>Owner:</b></td>
                                <td>$ownerName</td>
                              </tr>";
                        $mail_cont.="<tr style='font-weight: bold; border: 1px solid #fff;' ><td colspan='6'>$contactsTxt</td></tr>";                    
                        $mail_cont.="</table>";
                      
                        $mail_cont.="<table cellpadding='5' cellspacing='0' style='border: 1px solid #fff; border-collapse: collapse;width: 800px;'>";
                        $mail_cont.="<tr style='font-weight: bold; border: 1px solid #fff;' ><td style='width:160px;'>Hour</td><td>Device</td><td>Model</td><td>Error</td><td>Attempts</td><td>Action</td><td>Status </td></tr>";
                        

                        foreach($htmlDevices as $deviceLine)   {
                            $mail_cont.= $deviceLine;
                           
                        } 

                        $mail_cont.="</table><br><br>";
                        $mail_cont.="<br><br>";
                        $mail_cont.="<table border='0' cellpadding='1' cellspacing='0' >";
                        //$mail_cont.= "<br/><tr>You received this email because your email address is registered as the owner of a DC PhotoBooth</tr></br>";
                        $mail_cont.= "<br/><tr>Digital Centre</tr>";
                        $mail_cont.="</table>";
                        
//                        print $mail_cont;
                        include('../common/APP_mail.php'); //si el posem fora del foreach enviaria un sol missatge per a tots els PB, potser millor
                        $mail_cont ="";
                        
                
           
    

}
if($iiin){
    print " UPDATE App_infoDeviceMgt SET sent=1 where idDM IN $idDM_In ".$iiin;
    if($APP_BdD->Execute("UPDATE App_infoDeviceMgt SET sent=1 where idDM IN $idDM_In ")){
        //echo "OK";
    }else{
        echo "Error , no s'ha pogut actualitzar enviats";
    }
}


//$mail_cont.="<br><br>";
//$mail_cont.="<table border='0' cellpadding='1' cellspacing='0' >";
////$mail_cont.= "<br/><tr>You received this email because your email address is registered as the owner of a DC PhotoBooth</tr></br>";
//$mail_cont.= "<br/><tr>Digital Centre</tr>";
//$mail_cont.="</table>";
//print $mail_cont; 
//include('../common/APP_mail.php'); //si el posem fora del foreach enviaria un sol missatge per a tots els PB, potser millor

 $APP_BdD->CloseRs();
?>
