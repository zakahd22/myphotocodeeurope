<?php
require_once G_PATH . "models/baseModel.php";

class App_infoDeviceMgtModel extends baseModel{


    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getApp_infoDeviceMgt(){
        return $this->select('App_infoDeviceMgt');
    }
   
    //Si cal fer-lo s'hauria d'editar tenint en compte tots els camps, no id
    //Però no caldrà perquè això és un registre de les versions que van comunicant els PBs a UPGRADEcheck.php
//    public function updateApp_infoDeviceMgt($idbDCAll, $updates){
//        $this->setFilter('id', '=', $idbDCAll);      
//        return $this->update('App_infoDeviceMgt', $updates);
//    }
    
    public function getUPGRADEids(){
        $this->setGroup('App_infoDeviceMgt.idBooth');
        return $this->select('App_infoDeviceMgt');
    }
    
//    public function getActiveDeviceAlertsByBooth($pb_id){
//        $this->setOrder('when', 'DESC');
//        $this->setGroup('App_infoDeviceMgt.idBooth');
//        $this->setFilter('idBooth', '=', $pb_id);
//        $this->setFilter('ok', '=', 0, "AND");
//        $result = $this->select('App_infoDeviceMgt');
//        utils::log("Consulta amb getActiveDeviceAlertsByBooth".$this->get_sql_string(), logMoment);
//        $numDevicealerts = 0; 
//        if(count($result)){
//           $numDevicealerts = 1; 
//        }
//        return $numDevicealerts;
//        
//    }
    /*
     * Torna l'ultim ok. si és igual a 1, vol dir que està solucionat. Per tant retornem $numDevicealerts=0 (SENSE ALERTES OBERTES)
     */
    public function getActiveDeviceAlertsByBooth($pb_id){
        $this->setOrder('when', 'DESC');
        $this->setGroup('App_infoDeviceMgt.idBooth');
        $this->setFilter('idBooth', '=', $pb_id);  
        $this->setLimit(1);
        $result = $this->select('App_infoDeviceMgt');
//20250111PBlist utils::log("Consulta amb getActiveDeviceAlertsByBooth".$this->get_sql_string(), logMoment);
utils::log("Consulta amb getActiveDeviceAlertsByBooth".$this->get_sql_string(), 'logMoment');//20250111PBlist 
        $numDevicealerts = 0; 
        if(count($result) && $result[0]['ok']==0){
           $numDevicealerts = 1; 
        }
        return $numDevicealerts;       
        
    }
    
    
     public function getDeviceAlertsByBoothHtml($pb_id){
         
        
         $contactsTxt = "";   
         $sql = "SELECT CLD_Contactes.email, CLD_Contactes.phone, CLD_Contactes.city FROM App_booths
                     LEFT JOIN  CLD_Contactes ON CLD_Contactes.rental_id=App_booths.owner
                     WHERE App_booths.idBooth = $pb_id";
   
            $query = $this->my_query($sql);
            if($query){
            
            while($contactInfo = $this->my_fetch_array($query)){  
               
                
            $contactsTxt .= "<tr style='border: 2px solid #000;' ><td><b>Email:</b></td><td colspan='3'> ".$contactInfo['email']." <td><b>Phone:</b></td><td> ".$contactInfo['phone']." (".$contactInfo['city'].")</td></tr>";
                
            }
        }    
        
        $sql = "SELECT aidm.idDM, aidm.when, aidm.idBooth, aidm.idDongle, aidm.device, aidm.model, aidm.attempts, aidm.resp, aidm.action, aidm.ok, aidm.sent, aidm.pbs_time , aidm.db_time , ab.name as pbName, ab.serialnumber, ab.location, r.id AS ownerId, r.name as ownerName, r.App_email, b.rand_string, cbt.name as typeName, ab.version
                FROM App_infoDeviceMgt aidm 
                LEFT JOIN App_booths ab ON ab.idBooth=aidm.idBooth
                LEFT JOIN rentals r ON ab.owner=r.id
                LEFT JOIN booths b ON b.id=aidm.idDongle
                LEFT JOIN CLD_boothTypes cbt ON ab.CLD_idType = cbt.id 
                WHERE aidm.idBooth = $pb_id ORDER BY aidm.`when` DESC";      
        $query = $this->my_query($sql);
//20250111PBlist         utils::log("Consulta amb getDeviceAlertsByBoothHtml  ".$this->get_sql_string(), logMoment);
        utils::log("Consulta amb getDeviceAlertsByBoothHtml  ".$this->get_sql_string(), 'logMoment');//20250111PBlist 
        if($query){
            $i = 0;
            $result = array();
            
        //$result = $this->requestQueryResults($query, 'all', array('App_infoDeviceMgt', 'App_booths', 'rentals', 'booths', 'CLD_boothTypes'));
        /**
         * Generem Html DeviceMgt
         */ 
            
            $htmlDM.="<table border='0' cellpadding='5' cellspacing='0'  style='background-color: #e8f7ff; width: 800px;'>";
            $htmlDM.="<tr style='font-weight: bold; border: 1px solid #fff;' ><td colspan='6'>$contactsTxt</td></tr>";                    
            $htmlDM.="</table>";
            $htmlDM.="<table cellpadding='5' cellspacing='0' style='border: 1px solid #fff; border-collapse: collapse;width: 800px;'>";  
            $htmlDM.="<tr style='font-weight: bold; border: 1px solid #fff;' ><td style='width:160px;'>Hour</td><td>Device</td><td>Model</td><td>Error</td><td>Attempts</td><td>Action</td><td>Status </td></tr>";
            while($deviceLineValue = $this->my_fetch_array($query)){

                        
                                                    
                        
                      
                        
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
                $htmlDM.="<tr style='".$backgroundLine." border: 1px solid #fff;'><td style='border: 1px solid #fff;'>".$APP_araTimeSerial."</td><td style='border: 1px solid #fff;'> ".$deviceTxt." </td><td style='border: 1px solid #fff;'> ".$model." </td><td style='border: 1px solid #fff;'> ".$errorCode." </td><td style='border: 1px solid #fff;'>".$attemptsTxt."</td>"."<td style='border: 1px solid #fff;'>".$actionTxt."</td><td style='border: 1px solid #fff;'>".$recoveredTxt." </td></tr>";


                



                       
                        

            }
            $htmlDM.="</table>";
        
        /*
         * Fi Html Devicemgt
         */
        }
        
        return $htmlDM;
        
    }

    
    
    
    public function getDeviceAlertsAllPBsHtml($sn){
         
           $lastDateInt =  0;   
           $lastIdBooth  = 0;     
//         $contactsTxt = "";   
//         $sql = "SELECT CLD_Contactes.email, CLD_Contactes.phone, CLD_Contactes.city FROM App_booths
//                     LEFT JOIN  CLD_Contactes ON CLD_Contactes.rental_id=App_booths.owner
//                     WHERE App_booths.serialnumber LIKE '%$sn%'";
//   
//            $query = $this->my_query($sql);
//            if($query){
//            
//            while($contactInfo = $this->my_fetch_array($query)){  
//               
//                
//            $contactsTxt .= "<tr style='border: 2px solid #000;' ><td><b>Email:</b></td><td colspan='3'> ".$contactInfo['email']." <td><b>Phone:</b></td><td> ".$contactInfo['phone']." (".$contactInfo['city'].")</td></tr>";
//                
//            }
//        }    
        
        $sql = "SELECT aidm.idDM, aidm.when, aidm.idBooth, aidm.idDongle, aidm.device, aidm.model, aidm.attempts, aidm.resp, aidm.action, aidm.ok, aidm.sent, aidm.pbs_time , aidm.db_time , ab.name as pbName, ab.serialnumber, ab.location, r.id AS ownerId, r.name as ownerName, r.App_email, b.rand_string, cbt.name as typeName, ab.version
                FROM App_infoDeviceMgt aidm 
                LEFT JOIN App_booths ab ON ab.idBooth=aidm.idBooth
                LEFT JOIN rentals r ON ab.owner=r.id
                LEFT JOIN booths b ON b.id=aidm.idDongle
                LEFT JOIN CLD_boothTypes cbt ON ab.CLD_idType = cbt.id ";     
        
        if($sn) {
                $sql .= " WHERE ab.serialnumber LIKE '%$sn%'"; 
                
        }
        $sql .= "        ORDER BY aidm.`when` DESC";     
        
        $query = $this->my_query($sql);
        
        if($query){
            $i = 0;
            $result = array();
            
        utils::log("Consulta amb getDeviceAlertsAllPBsHtml  ".$this->get_sql_string(), logMoment);
        /**
         * Generem Html DeviceMgt
         */ 
            
            $htmlDM.="<table border='0' cellpadding='5' cellspacing='5'  style='background-color: #e8f7ff; width: 800px;'>";
            $htmlDM.="<tr style='font-weight: bold; border: 1px solid #fff;' ><td colspan='6'>$contactsTxt</td></tr>";                    
            $htmlDM.="</table>";
            $htmlDM.="<table cellpadding='5' cellspacing='0' style='border: 1px solid #fff; border-collapse: collapse;width: 800px;' class='taulaDevAlert'>";  
            
            while($deviceLineValue = $this->my_fetch_array($query)){

                        
                                                    
                        
                      
                        
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
                 $htmlHeader="<tr style='font-weight: bold; border: 1px solid #fff;' ><td style='width:160px;'>Hour</td><td>Device</td><td>Model</td><td>Error</td><td>Attempts</td><td>Action</td><td>Status </td></tr>";   
                 if($lastIdBooth  != $idBooth){
                     $htmlDM.="<tr><td colspan='7' style='border-bottom: 1px solid #000; height:30px;'></td></tr>";
                     //$htmlDM.="<tr style='background-color: #ccc; border: 1px solid #fff;'><td style='border: 1px solid #fff;'>".$pbName."</td><td style='border: 1px solid #fff;'> ".$serialnumber." </td><td style='border: 1px solid #fff;'> ".$string." </td><td style='border: 1px solid #fff;'> ".$pbVersion." </td><td style='border: 1px solid #fff;'>".$idBooth."</td>"."<td style='border: 1px solid #fff;'>".$ownerName."</td><td style='border: 1px solid #fff;'>".$location." </td></tr>";
                     $htmlDM.="<tr style='background-color: #ccc; border: 1px solid #fff;'><td colspan='7' style='border: 1px solid #fff;' onclick='openLink(\"PhotoBooths\" ,$idBooth);'>".$serialnumber." - ".$pbVersion."</td></tr>";
                     if(!$sn ){
                        $htmlDM.=$htmlHeader; 
                     }   
                     
                 }
                $quan = new DateTime($APP_araTimeSerial);
                $dateInt = intval($quan->format("Ymd"));
                $dateMostraDia = $quan->format("Y-m-d");
                if($sn &&$lastDateInt !=  $dateInt){                    
                    $htmlDM.="<tr><td colspan='7' style='border-bottom: 1px solid #000; height:30px;'>".$dateMostraDia."</td></tr>";
                    $htmlDM.=$htmlHeader; 
                    
                }
                $htmlDM.="<tr style='".$backgroundLine." border: 1px solid #fff;'><td style='border: 1px solid #fff;'>".$APP_araTimeSerial."</td><td style='border: 1px solid #fff;'> ".$deviceTxt." </td><td style='border: 1px solid #fff;'> ".$model." </td><td style='border: 1px solid #fff;'> ".$errorCode." </td><td style='border: 1px solid #fff;'>".$attemptsTxt."</td>"."<td style='border: 1px solid #fff;'>".$actionTxt."</td><td style='border: 1px solid #fff;'>".$recoveredTxt." </td></tr>";


                

                
                    
                $lastDateInt =  $dateInt;   
                $lastIdBooth  = $idBooth; 

            }
            $htmlDM.="</table>";
        
        /*
         * Fi Html Devicemgt
         */
        }
        
        return $htmlDM;
        
    }


    
}

