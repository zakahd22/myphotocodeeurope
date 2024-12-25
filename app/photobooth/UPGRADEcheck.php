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
        
$APP_BdD->CloseRs();
//#Britta316upgrade
//comento el codi anterior, començarem amb upgrade de 3.1.6. Els PBs enviaran UPGRADEid B3.1.5 !!! Cal comprovar bootDC per a discriminar
        
//switch($UPGRADEid){
//    case "B3.1.5":
////    APP_fesLogDebbug(" TRACE UPGRADEid: $UPGRADEid. APP_idBooth: $APP_idBooth. bootDC: {$_REQUEST['bootDC']}. getB315: ".getB315($bootDC), "logs/logUpgrade.dat"); 
//        APP_fesLogDebbug(" TRACE UPGRADEid: $UPGRADEid. APP_idBooth: $APP_idBooth. $nomExe bootDC: *{$_REQUEST['bootDC']}*. getB315: ".getB315($_REQUEST['bootDC'],$APP_idBooth), "logs/logUpgrade.dat"); 
//
////fet        if($APP_idBooth == 8089){
////        if($APP_idBooth == 8358){
//            echo getB315($_REQUEST['bootDC'],$APP_idBooth);
//            return;
////        }
//
//        break;
//    case "B3.1.6":
//        APP_fesLogDebbug(" TRACE UPGRADEid: $UPGRADEid. APP_idBooth: $APP_idBooth. $nomExe bootDC: *{$_REQUEST['bootDC']}*. ", "logs/logUpgrade.dat"); 
//        break;
//}
//echo  "ok#";
        
//function getB315($bootDC,$idBooth)
//{
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    switch($bootDC){
////    case "20211022 NG 7700 9550 Britta V3.1.6":
////        return "ok#upB316ng9550";
////    case "20211025 NG 7700 D80 Britta V3.1.6":
////        return "ok#upB316ngD80";
////    case "20211022 HOUSE 8000 9810 Britta V3.1.6":
////        return "ok#upB316ng9810";
////    case "20211020 NG 8000 D80 Britta V3.1.6":
////        return "ok#upB316ngD80";
////    case "20211025 ECLIPSE 8300  9810 Britta V3.1.6":
////        return "ok#upB316ng9810";
////    case "20211026 ECLIPSE 8300 RX1 Britta V3.1.6 Tecnotron":
////        return "ok#upB316ngRX1";
////    case "20211026 Revolution 8300 D80 Britta V3.1.6":
////        return "ok#upB316ng9810";      
//        
//        
//        
//    case "20211026 Revolution 8300 D80 Britta v3.1.6":
//        
//        $arrOk = array(8089,8034); if (!in_array($idBooth, $arrOk)) return "ok#";
//        
//        return "ok#upB316ngD80#1";
//        
//    case "20211027 Dlight 8300 9810 Britta v3.1.6":
//        
//        $arrOk = array(8378); if (!in_array($idBooth, $arrOk)) return "ok#";
//        
//        return "ok#upB316ng9810#1";
//        
//    case "20211025 Eclipse 8300 9810 Britta v3.1.6":
//        
//        $arrOk = array(8122); if (!in_array($idBooth, $arrOk)) return "ok#";
//        
//        return "ok#upB316ng9810#1";
//    }
//    return "ok#";//no cal update
//}
//
        
//
//$tipusPB = substr($idmaq, 0, 1);
//	//A Strip sense def
//	//B Wall (P12)
//	//C MegaIn ()
//	//D MegaOut (KG4viewer fins 04/03/2012; despres (OUT)
//	//E Party (PNG)
//	//F NewGeneration (NG)
//	//G IPS
//	//H Igo (IGO)
//	//J MegaOutMail (saltem la I)
//	//K Arena (un altre projecte!!!!!!)
//	//L NG2P
//	//M MEGA2P
//	//N PUBLI
//
//
////APP_fesLog("Trace 20150220UpLog - UPGRADEid: $UPGRADEid, tipusPB: $tipusPB ");
//
//
//switch($UPGRADEid){
//    case "2a":
//        switch($tipusPB){
//        case 'F':
//            echo "ok#21#1";//exe i un cab
//            break;
//        case 'A':
//        case 'H':
//        case 'K': ///?????????
//        case 'L':
//        case 'M':
//        case 'N':
//            //old products
//        case 'B':
//        case 'C':
//        case 'D':
//        case 'E':
//        case 'G':
//        case 'I':
//            echo "ok#"; //no cal update
//            break;
//        }
//        return;
//    case "20":
//        echo "ok#"; //no cal update
//        return;
//    case "21":
//        echo "ok#"; //no cal update
//        return;
//        
////201502 INICI        
//    case "N0b":
//        switch($tipusPB){
//        case 'F':
//            echo "ok#N01#2";//exe i 2 cab
//            return;
//        }
//        break;
//    case "N01":
////20150709 INICI
//        APP_fesLog("UPGRADEcheck de N01 APP_idBooth: $APP_idBooth. bootDC: ".$_REQUEST['bootDC']);
//        if($APP_idBooth == 6487){//DCA 2n STRIP a DB AB6Z  (hi ha una ja instalada: 482)
//            echo "ok#N0B#1";//exe i 1 cab
//            return;
//        }
////20150709 FINAL
//        
//        
//        
//        echo "ok#"; //no cal update
//        return;
////201502 FINAL        
//}


////20151026 echo "ko#Invalid UPGRADEid param: $UPGRADEid";
//echo  "ok#";//20151026 

