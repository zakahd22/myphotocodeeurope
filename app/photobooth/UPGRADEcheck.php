<?php
$APP_common_no_idb = true;//20140626


//20150220noLog

require("common.php");

//APP_fesLog("Trace 20150220UpLog - dongle: {$_REQUEST['dongle']}, idmaq: {$_REQUEST['idmaq']}, UPGRADEid : {$_REQUEST['UPGRADEid']},  UPGRADEbootDC: {$_REQUEST['UPGRADEbootDC']} ");

if(!$APP_dongleOK) return;


// sense base de dades, només php
//upgrade from 2a to 20

if(isset($_REQUEST['UPGRADEid'])){ $UPGRADEid = $_REQUEST['UPGRADEid'];}
else{
echo "ko#No UPGRADEid param";
return;
}



if(isset($_REQUEST['name'])){ $nomExe = $_REQUEST['name'];}
else {$nomExe = "";}

//202301 upgrade INICI
//decidirem per name, ens envia nomExe-hw
if($UPGRADEid == "Bv4.0"){
    APP_fesLogDebbug(" TRACE UPGRADEid: $UPGRADEid. APP_idBooth: $APP_idBooth. Name: $nomExe,  bootDC: *{$_REQUEST['bootDC']}*.", "logs/logUpgrade.dat"); 
    if(strlen($nomExe) >= 6){
        $tmpExe = substr($nomExe, 0, 6);
        switch($tmpExe){
            case "NG-D80":
                $arrOk = array(7583,8405,7712); if (!in_array($APP_idBooth, $arrOk)) echo "ok#"; else //control idPB, enviem que no cal upgrade
                echo "ok#upgB401ngD80#7";
                return;
            case "NG-D90":
                $arrOk = array(7583,8405,7712); if (!in_array($APP_idBooth, $arrOk)) echo "ok#"; else //control idPB, enviem que no cal upgrade
                echo "ok#upgB401ngD90#7";
                return;
            case "NG-RX1":
                $arrOk = array(7583,7583,8380); if (!in_array($APP_idBooth, $arrOk)) echo "ok#"; else //control idPB, enviem que no cal upgrade
                echo "ok#upgB401ngRX1#7";
                return;
        }
    }
    if(strlen($nomExe) >= 7){
        $tmpExe = substr($nomExe, 0, 7);
        APP_fesLogDebbug(" TRACE UPGRADEid: $UPGRADEid. APP_idBooth: $APP_idBooth. tmpExe: *$tmpExe*", "logs/logUpgrade.dat"); 
        switch($tmpExe){
            case "NG-9550":
                $arrOk = array(7583,7712); if (!in_array($APP_idBooth, $arrOk)) echo "ok#"; else //control idPB, enviem que no cal upgrade
                echo "ok#upgB401ng9550#7";
                APP_fesLogDebbug(" TRACE UPGRADEid: $UPGRADEid. APP_idBooth: $APP_idBooth. Name: $nomExe,  resp: *ok#upgB401ng9550#7*.", "logs/logUpgrade.dat"); 
                return;
            case "NG-9810":
                $arrOk = array(7583,8358,7712); if (!in_array($APP_idBooth, $arrOk)) echo "ok#"; else //control idPB, enviem que no cal upgrade
                echo "ok#upgB401ng9810#7";
                return;
        }
    }
    
}

//202301 upgrade FINAL

/******
 * 
 * GUARDEM A BD EL bootDC * 
 * Eloi 20220409
 * 
 */

if(isset($_REQUEST['bootDC'])){ $bootDC = $_REQUEST['bootDC'];}
else {$bootDC = "";}

if(isset($bootDC) && $bootDC != ""){
    //Comprovem si existeix la textLine del bootDC
    $sql = "SELECT idBootDC FROM App_bootDC WHERE textLine='".$bootDC."'  LIMIT 0,1";    
    
    $esOK = $APP_BdD->OpenRs($sql);
    if($esOK){
        if($APP_BdD->FetchRs()){                
            $idBootDC = $APP_BdD->GetField(1);  
        }else{
            //fem insert BD
            $sql = "INSERT INTO App_bootDC SET textLine='".$bootDC."'";
            $idBootDC = $APP_BdD->ExecuteInsert($sql);
        }
      $APP_BdD->CloseRs();
    }
    
    //Comprovem si existeix la textLine del bootDC per aquest PB
    $sql = "SELECT * FROM App_boothBootDC WHERE idBooth='".$APP_idBooth."' AND idDongle='".$APP_idDongle."' AND UPGRADEid='".$UPGRADEid."' AND idBootDC='".$idBootDC."'  LIMIT 0,1 ";
   
    $esOK = $APP_BdD->OpenRs($sql);
    if($esOK){
         if(!$APP_BdD->FetchRs()){  
               //fem insert BD
               $sql = "INSERT INTO App_boothBootDC SET  idBooth='".$APP_idBooth."', idDongle='".$APP_idDongle."', UPGRADEid='".$UPGRADEid."', idBootDC='".$idBootDC."'";
               $App_bootDC = $APP_BdD->ExecuteInsert($sql);
              
         }  
         $APP_BdD->CloseRs();
    }
     
    
    
}
      
      
 /****** * 
 * Fi   GUARDEM A BD EL bootDC *  * 
 */  





/* 20220502 #UpgradePBs
* Consulta a MyPC per a saber si hi ha actualitzacions. Consulta la taula App_bootDCAllowed si l’id del Booth en la versió que té instalada (UPGRADEid) té alguna actualització pendent.
* Si el camp allowedIds està a NULL, l’actualització afecta a tots els PBs.
*/

$sql = "SELECT * FROM App_bootDCAllowed "       
        . "WHERE (allowedIds IS NULL OR allowedIds='$APP_idBooth') AND  App_bootDCAllowed.UPGRADEid	 = '$UPGRADEid' AND idBootDC='".$idBootDC."'";

        
        
$esOK = $APP_BdD->OpenRs($sql);
if($esOK){
   if($APP_BdD->FetchRs()){                
       $response = $APP_BdD->GetField(5);  
       $APP_BdD->CloseRs();
       $esOK = $APP_BdD->OpenRs($sql);
       if($esOK){
       $sql = "UPDATE App_boothBootDC SET  ok=1 "
                    . " WHERE idBooth='".$APP_idBooth."' AND idDongle='".$APP_idDongle."' AND UPGRADEid='".$UPGRADEid."' AND idBootDC='".$idBootDC."'";
            $updateBoothBootDC = $APP_BdD->Execute($sql);
            
       }     
       $APP_BdD->CloseRs();
   }else{
       $response = "ok#";
   }
 $APP_BdD->CloseRs();
}    
        
print $response;
        
