<?php

/*
 * El PB envia informació de ClientID de TW
 * 202201sg: el PB no está enviant correctament la signatura, de fet tampoc s'està generant correctament el seccode a la taula booths; ni es guarda al dongle
 *          desactivo control de sg
 */
$APP_common_no_idb = true;


require("common.php");
if(!$APP_dongleOK) return;

$myScript = "PBtw_id";

//202201sg INICI
//if(!$APP_sg){echo "Error sg"; APP_fesLog("Error - $myScript, sg is empty. idPB: $APP_idBooth"); return;}
//if(!$APP_tact){echo "Error tact"; APP_fesLog("Error - $myScript, tact is empty");  return;}
/////////if(!$APP_sg){ APP_fesLogDebbug("warning, sg is empty. idPB: $APP_idBooth; dongle: $APP_dongle","logs/logDebug202201sg");}

//202201sg FIAL

// paràmetres
if(isset($_POST['twid'])){ $twid = $_POST['twid'];} else {echo "Error id"; APP_fesLog("Error - $myScript, twid is empty");  return;}

if(!$twid){
    $sqltwid = "NULL";
}
else{
    $sqltwid = "'$twid'";
   
}

//202201sg INICI
//
////signatura:
//$signature = strtoupper(sha1($twid.$APP_dongle.$APP_tact.$APP_seccode));
//if($signature != $APP_sg){
//    APP_fesLog("Error - $myScript, sg error local: $signature url:$APP_sg");
//    echo "Error - sg";
//    return;
//}
//202201sg FINAL

$sql = "UPDATE App_booths SET PBtwid=$sqltwid WHERE idBooth=$APP_idBooth;"; 
$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    APP_fesLog("Error $myScript - Database update: $sql");
//    echo "Error - Database update: $sql.";
//    $APP_common_error = true;
    return;

}
        $trace = "trace-".$_POST['alrt'];
//enviament de mail
if(isset($_POST['alrt'])){
    $calMail = intval($_POST['alrt']);
        $trace.= "-calMail-$calMail-";
    if($calMail == 1){
        
        
        $mail_replayto = "main@dc-image.com";
        $mail_email = "main@dc-image.com";

        $mail_remitent = "main@dc-image.com";
        $mail_nomremitent = "DC Alerts Platform";

//        $mail_copia1 = "jtarres@dc-image.com";
//        $mail_copianom1 = "";
//        $mail_copia2 = "info@dc-image.com";
//        $mail_copianom2 = "";
//        $mail_copia3 = "eloi@dc-image.com";
//        $mail_copianom3 = "";
        
        $mail_subject = "id for remote support";

        $mail_cont = "<h1>A clientID for remote support has been received</h1>";
        
        $mail_cont.= "<p>Remote ClientID: $twid</p>";
        
        
        
        

        $mail_cont.= "Photobooth ID: $APP_idBooth<br/>";
        $mail_cont.= "String: $APP_rand_string<br/>";
        $mail_cont.= "Photobooth name: $APP_nameBooth<br/>";
        
        
        
//20160829mesDades INICI        
//        $mail_cont.= "Owner ID: $APP_idRental</p>";
        ////SELECT `idBooth`, `estat`, `type`, `owner`, `name`, `obs`, `serialnumber`, `location`, `latitude`, `longitude`, `alertOffline`, `hS`, `mS`, `hE`, `mE`, `report` FROM `App_booths` WHERE 1
        $sql = "SELECT `serialnumber`, `version` FROM `App_booths` WHERE idBooth=$APP_idBooth;"; 
        $esOK = $APP_BdD->OpenRs($sql);
        if($esOK){
             if($APP_BdD->FetchRs()){
                $serialN =  $APP_BdD->GetField(1);
                $version =  $APP_BdD->GetField(2);
             }
             $APP_BdD->CloseRs();
        }
        $mail_cont.= "Photobooth Serial Number: $serialN<br/>";
        $mail_cont.= "Photobooth Version: $version<br/>";
        
        $mail_cont.= "Owner ID: $APP_idRental<br/>";
        $sql = "SELECT name,`App_email` FROM rentals WHERE id=$APP_idRental; ";
        $esOK = $APP_BdD->OpenRs($sql);
        if($esOK){
             if($APP_BdD->FetchRs()){
                $ownerNom =  $APP_BdD->GetField(1);
                $ownerMail =  $APP_BdD->GetField(2);
             }
             $APP_BdD->CloseRs();
        }
        $mail_cont.= "Owner Name: $ownerNom<br/>";
        $mail_cont.= "Owner Mail: $ownerMail<br/>";
        $mail_cont.= "</p>";
//20160829mesDades FINAL        
        
        
        
        
        

        //?                    include("../../common/APP_mail.php");
        include('../common/APP_mail.php');
        
        $trace.= "$mail_ret-$mail_retMsg";
    }
}




echo "ok#$trace";//res més

?>
