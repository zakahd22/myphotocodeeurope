<?php //
//potser més endavant ignore_user_abort(true);//20130524 per a que el procès de missatges APNS no interfereixi amb la resposta

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
//error_reporting(E_ALL ^ E_WARNING); // Error/Exception engine, E_ALL except Warnings
ini_set('ignore_repeated_errors', TRUE); // always use TRUE
ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', "../../../logsMyPC/log_reporting_app_photobooth-".date("Ymd").".dat"); // Logging file path


//error_reporting(0);
//ini_set('display_errors', 0);

//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar

date_default_timezone_set(@date_default_timezone_get());

//20170220apns $APNS_MessageAdded = false;//20130524 per a fer $apns->processQueue(); al final de tot
//


require("../common/APP_common.php");

//20211123PBupload INICI
$esPBupload = false;
$esPBuploadOk = false;
$msgPBupload = "";
include_once 'common/PBupload.php';
if($esPBupload){
    if(!$esPBuploadOk){
        APP_fesLogDebbug("  -> Error PBupload - $msgPBupload", "logs/logPBupload.dat");
        die("Error PBupload - $msgPBupload");
        
    }
}
//20211123PBupload FINAL

//20201022 INICI
//require("../common/APP_BdD.php");
if($APP_common_mevaBdD){
    require("../common/APP_BdD_bk.php");
}
else{
    require("../common/APP_BdD.php");
}
//20201022 FINAL



//20170629pb INICI
//llegirem el paràmetre 'pb', pot ser 1 (Britta fins a 2.1.0.6) o 21 (Britta a partir de 2.1.0.6)
    if(isset($_REQUEST['pb'])){
        $APP_pbSql = ",PBnew=".$_REQUEST['pb'];
    }
//20170629pb FINAL




//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//SELECT `id`, `dongle`, `reference`, `rand_string`, `rental_id` FROM `booths` WHERE 1
//SELECT `idInfo`, `when`, `idBooth`, `idDongle`, `typeInfo`, `money`, `currency`, `stock`, `i1`, `i2`, `i3`, `str1`, `str2` FROM `App_info` WHERE 1
//SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_boothDongle` WHERE 1
////taula App_booths SELECT `idBooth`,`type`,`owner`,`name`,`obs`,`serialnumber`,`location`,`latitude`,`longitude`, FROM `App_booths` WHERE 1
//SELECT `id`, `idBooth`, `idDongle`, `start`, `last` FROM `App_sessions` WHERE 1

// checkUser(){
$APP_dongleOK = false;
$APP_idBooth = "";
$APP_idDongle = "";
$APP_nameBooth = "";//20130522
$APP_locationBooth = "";//20150625location

$APP_tzBooth = "";//20131003 time zone assignat al PB

$APP_dongle = "";//20160112
$APP_seccode = "";//20160112seccode per a seguretat en comunicacions
$APP_sg = "";//20160112seccode per a seguretat en comunicacions


if(isset($_REQUEST['sg'])){ $APP_sg = $_REQUEST['sg'];} else {$APP_sg = "";}//20160112seccode per a seguretat en comunicacions
if(isset($_REQUEST['tact'])){ $APP_tact = $_REQUEST['tact'];} else {$APP_tact = "";}//20160112seccode per a seguretat en comunicacions, i per a calcular el desfase entre servidor i client
//20170627tact INICI
//Britta 1.1 envia tact i sg sense separació de &
$APP_tactSql = "";
if((strlen($APP_tact) == 14)) $APP_tactSql = $APP_BdD->myDateTimeSerialFull($APP_tact);
if(!$APP_sg){
    if(strlen($APP_tact) > 17){
        //suposem que és el problema
        $APP_sg = substr($APP_tact, 17);
        $APP_tact = substr($APP_tact, 0, 14);
        //a més comprovaré si és una data correcta
        try{
//            $tmp = $APP_BdD->myDateTimeSerialFull($APP_tact);
            $tmpDateTime = DateTime::createFromFormat("YmdHis", $APP_tact);
            if(!$tmpDateTime){
                APP_fesLogDebbug("Error 20170627-01 ({$_REQUEST['idb']},$APP_tact,$tmp) tact: {$_REQUEST['tact']}","logDebug20170627tact");
                APP_fesLogDebbug("Error 20170627-01bis ".$tmpDateTime->format("d/m/Y"),"logDebug20170627tact");
            }
            else{
//                $sql.=",pbs_time=$tmpdate ";
                $APP_tactSql = $APP_BdD->myDateTimeSerialFull($APP_tact);
                APP_fesLogDebbug("Error 20170627-ok ({$_REQUEST['idb']},$APP_tact) APP_tactSql: $APP_tactSql,  tact: {$_REQUEST['tact']}","logDebug20170627tact");
            }
        }
        catch (Exception $e) {
            //res
            APP_fesLogDebbug("Error 20170627-02 ({$_REQUEST['idb']},$APP_tact) tact: {$_REQUEST['tact']}","logDebug20170627tact");
        }
        

    }
}
//20170627tact FINAL

if($APP_BdD_error){
    echo "Error - Database access: $APP_BdD_error.";
    return;
}

$sql = "";

$APP_rand_string = "";//20150626

if(isset($_REQUEST['dongle'])){
    $APP_dongle = $_REQUEST['dongle'];
    if(isset($_REQUEST['idmaq'])){
        $idmaq = $_REQUEST['idmaq'];
        //comprovem el dongle
        if(strlen($idmaq) != 4){
            echo  "Error - code 01";
            return;
            
        }
        $rand_string = substr($idmaq, 1);
        $APP_rand_string = $rand_string;
        

//20170616idPB        $sql = "SELECT App_boothDongle.idBooth,booths.id,booths.rental_id,`App_booths`.name,`App_booths`.`timeZone`,`App_booths`.location,booths.seccode, booths.CLD_Distributor FROM booths LEFT JOIN App_boothDongle ON booths.id = App_boothDongle.idDongle LEFT JOIN `App_booths` ON App_boothDongle.idBooth = `App_booths`.idBooth  WHERE dongle='$APP_dongle' AND `rand_string`='$rand_string'  ORDER BY datetimeS DESC LIMIT 0,1; ";//20160112seccode
        $sql = "SELECT App_boothDongle.idBooth,booths.id,booths.rental_id,`App_booths`.name,`App_booths`.`timeZone`,`App_booths`.location,booths.seccode, booths.CLD_Distributor, idPBwrong, idPBright FROM booths LEFT JOIN App_boothDongle ON booths.id = App_boothDongle.idDongle LEFT JOIN `App_booths` ON App_boothDongle.idBooth = `App_booths`.idBooth  WHERE dongle='$APP_dongle' AND `rand_string`='$rand_string'  ORDER BY datetimeS DESC LIMIT 0,1; ";//20170616idPB

        
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
        //caldria controlar l'error
            echo "Error - code 02 $sql";
            return;
        }
//        echo "$APP_xml TRACE 02</return>";
//        return;
        
        $APP_idBooth = "";
        $APP_idDongle = "";
        $APP_idRental = "";
        $APP_nameBooth = "";//20130522
        $APP_tzBooth = "";//20131003 time zone assignat al PB
        $APP_CLD_Distributor = "";//20160512pxp
        
        
        if($APP_BdD->FetchRs()){
            $APP_dongleOK = true;
            $APP_idBooth = $APP_BdD->GetField(1);
            $APP_idDongle = $APP_BdD->GetField(2);
            $APP_idRental = $APP_BdD->GetField(3);
            
            $APP_nameBooth = APP_preparaXML($APP_BdD->GetField(4));//20130522
            
            $APP_tzBooth = $APP_BdD->GetField(5);//20131003 time zone assignat al PB
            
            $APP_locationBooth = $APP_BdD->GetField(6);//20150625location
            
            $APP_seccode = $APP_BdD->GetField(7);//20160112seccode
            $APP_CLD_Distributor = $APP_BdD->GetField(8);//20160512pxp
            
            $idPBwrong  = $APP_BdD->GetField(9);//20170616idPB
            $idPBright = $APP_BdD->GetField(10);//20170616idPB
            
            
        }
        $APP_BdD->CloseRs();
    }
    else{
        echo  "Error - code 03";
        return;
        
    }
}
else{
    echo  "Error - code 04";
    return;

}

if(!$APP_dongleOK){
    
        
    echo  "Error - code 05";
    return;


}


$ara = new DateTime("now");
$APP_araTimeSerial = $APP_BdD->myDateTimeSerial($ara);

if(isset($APP_common_no_idb)) return;//20140626)


//guardarem a App_sessions

//20131003 INICI
if($APP_tzBooth){
    $araTz = new DateTime("now",new DateTimeZone($APP_tzBooth));
$APP_araTzTimeSerial = $APP_BdD->myDateTimeSerial($araTz);

}
else{
    $APP_araTzTimeSerial = $APP_araTimeSerial;
}
//20131003 FINAL


if(isset($_REQUEST['t'])){
   
    
    if(!$_REQUEST['t']){ //20160607Error - code 52
        $APP_inTimeSerial = $APP_araTimeSerial;//20160607Error - code 52
        APP_fesLog("Warning  ( - code 52=  _REQUEST['t'] is empty");//20160607Error - code 52
    }//20160607Error - code 52
    else{
        //20160607Error - code 52
        $APP_inTimeSerial = $APP_BdD->myDateTimeSerialFull($_REQUEST['t']);
    }
    
    
    
     /**
      * 20220511
     * Establim que si la diferencia es de mes d'un dia (tant positiu com negatiu, que voldria dir que es una data futura) la hora que insertem es $APP_araTzTimeSerial
     */
    //20220609 HO TREIEM, SUBSTITUIREM PER UNA ALERTA EMAIL "La hora/dia del PB no coincideix amb now"
//    $tNow = date("YmdHis");
//    $tDiferencia = $tNow - $_REQUEST['t'];
//    if($tDiferencia >1200000 || $tDiferencia <-1200000){ //20220511 per donar marge a hores mal posades al pb. (1200000 un dia en el passat, -1200000 un  dia en el futur)
//        $APP_inTimeSerial = $APP_araTzTimeSerial;
//    }
    
    
} else {$APP_inTimeSerial = $APP_araTimeSerial;}



//idBooth, podria estar en blanc
if(isset($_REQUEST['idb'])){
    $idb = $_REQUEST['idb'];
    if(intval($idb) <= 0) $idb = "";
    
    } else {$idb = "";}

if(isset($_REQUEST['vr'])){ $ver = "'".$_REQUEST['vr']."'";} else {$ver = "NULL";}//20130424

//possibilitats:
// - idb en blanc (la màquina no sap qui és) s'acaba de fer un boot
//      - $APP_idBooth també en blanc (gran problema) si estem aqui és que el dongle existeix => nou App_booths i nou App_boothDongle
//      - $APP_idBooth amb valor (ja estava associat) informarem l'aplicació del seu id
// - idb amb valor
//      - $APP_idBooth en blanc (és una opció sense sentit, ja que si la màquina sap qui és és perque existeix a la BdD
//      - $APP_idBooth amb valor (ja estava associat) i igual a idb (Això és el que ha de passar sempre 99,9%)
//      - $APP_idBooth amb valor (ja estava associat) i diferent a idb (Han canviat la impressora de màquina) => nou App_boothDongle

//20130506 INICI simulem un retard en la resposta per als PB de DC
//if($APP_idBooth == 5 || $APP_idBooth == 12 || $APP_idBooth == 79){
//    APP_fesLog("\nTRACE extra sleep");
//    sleep(8);
//
//}

//20130506 FINAL


//20170616idPB INICI
if($idPBright){
    if(!$idb) {
        APP_fesLogDebbug("PB sense idPB, cal forçar a $idPBright", "logAPPchangePBiD.dat");
        $idb = $idPBright;
    }
    else if($idb == $idPBwrong){
        APP_fesLogDebbug("PB amb idPB incorrecte ($idPBwrong), cal forçar a $idPBright", "logAPPchangePBiD.dat");
        $idb = $idPBright;
        
    }
}
else //si no hi ha $idPBright cal comprovar if(!$APP_idBooth). Ho deixo en else
//20170616idPB FINAL
if(!$APP_idBooth) $idb= "";//20130502 INICI fem com si no hi hagés idb 


if(!$idb) {
    if(!$APP_idBooth) {
        //////taula App_booths SELECT `idBooth`,`type`,`owner`,`name`,`obs`,`serialnumber`,`location`,`latitude`,`longitude`, FROM `App_booths` WHERE 1
        $type = substr($idmaq, 0, 1);
        $APP_nameBooth = $idmaq;//20130522
//20130424        $sql = "INSERT INTO App_booths SET type='$type', owner=$APP_idRental, name='$idmaq', location='$idmaq';";
        $sql = "INSERT INTO App_booths SET type='$type', owner=$APP_idRental, name='$idmaq', location='$idmaq', `version`=$ver;";//20130424

        $APP_idBooth = $APP_BdD->ExecuteInsert($sql);
        if(!$APP_idBooth) {
            echo  "Error - Database insert: $sql.";
            return;

        }//SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_boothDongle`
        
//20130322        $sql = "INSERT INTO App_boothDongle SET idBooth=$APP_idBooth, idDongle=$APP_idDongle, datetimeS=$APP_inTimeSerial ;";
        
        $sql = "INSERT INTO App_boothDongle SET idBooth=$APP_idBooth, idDongle=$APP_idDongle, datetimeS=$APP_inTimeSerial 
            ON DUPLICATE KEY UPDATE idBooth=$APP_idBooth, idDongle=$APP_idDongle, datetimeS=$APP_inTimeSerial;";//20130322
        
        
        APP_fesLog("New booth/dongle pair: $APP_idBooth,$APP_idDongle,$idmaq");//20150529
        
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK) {
            echo  "Error - Database insert: $sql.";
            return;

        }
        
    }
    // retornarem sempre idBooth else{
    
}
else{//idb amb valor
    
//20160314 INICI
//ID erroni  ID Booth ID Dongle String
//7570  7587  933  0PG
    if($idb==7570 && $APP_idDongle==933){
        APP_fesLog("Incidancia 20160314 (APP_idBooth: $APP_idBooth) - Canviem 7559 per 7585!!!");
        $idb = 7587;
    }
//20160314 FINAL
    
//20170519 FINAL -- erroni, id de PB incorrecte, havia de ser 7208
//    if($idb==7881 && $APP_idDongle==1160){
//        APP_fesLog("Incidencia 20170519 -Financing Code (APP_idBooth: $APP_idBooth) - Canviem 7881 per 7208!!!");
//        $idb = 7208;
//    }
    
    if($idb==7208 && $APP_idDongle==1160){
        APP_fesLog("Incidencia 20170519 -Financing Code (APP_idBooth: $APP_idBooth) - Canviem 7881 per 7204!!!");
        $idb = 7204;
    }
    
    
    if($idb==755601 && $APP_idDongle==939){
        APP_fesLog("Incidencia 20191021 -Financing Code (APP_idBooth: $APP_idBooth) - Canviem 755601 per 8170!!!");
        $idb = 8170;
    }
    
//20170519 FINAL
    
   
//20160419 INICI    
//20170418    if($idb==7105 && $APP_idDongle==1027){
//        APP_fesLog("Incidencia 20160419 (APP_idBooth: $APP_idBooth) - Canviem 7105 per 7598!!!");
//        $idb = 7598;
//    }    
//20160419 FINAL    
    
//20170104 INICI      
//Id Photobooth 7642 – id dongle 1156 – string dongle HRS
//
//Id PhotoBooth 7637 – id dongle 1155 – string dongle URW

//    if($idb==7505 && $APP_idDongle==1156){
//        APP_fesLog("Incidencia 20170104 (APP_idBooth: $APP_idBooth) - Canviem 7505 per 7642!!!");
//        $idb = 7642;
//    }    
//
//    if($idb==7505 && $APP_idDongle==1155){
//        APP_fesLog("Incidencia 20170104 (APP_idBooth: $APP_idBooth) - Canviem 7505 per 7637!!!");
//        $idb = 7637;
//    }    
//20170104 FINAL      
    
//20170113 INICI        
//    if($idb==7203 && $APP_idDongle==510){
//        APP_fesLogDebbug("Incidencia 20170113 (APP_idBooth: $APP_idBooth) - Canviem 7203 per 205 (dongle 510)!!!","logAPPchangePBiD.dat");
//        $idb = 205;
//    }    
//    if($idb==7505 && $APP_idDongle==1049){
//        APP_fesLogDebbug("Incidencia 20170113 (APP_idBooth: $APP_idBooth) - Canviem 7505 per 7644 (dongle 1049)!!!","logAPPchangePBiD.dat");
//        $idb = 7644;
//    }    
//    if($idb==7505 && $APP_idDongle==1153){
//        APP_fesLogDebbug("Incidencia 20170113 (APP_idBooth: $APP_idBooth) - Canviem 7505 per 7638 (dongle 1153)!!!","logAPPchangePBiD.dat");
//        $idb = 7638;
//    }    
    
//20170113 FINAL        
    
        if($idb != $APP_idBooth){//Han canviat la impressora //20220121 o el pc, o el dongle... el fet es que l'idb, que es a l'arxiu PB/PBDC.dat no coincideix amb qui hauria de ser...
            
//20130926 INICI
            //cal omplir datetimeF dels registres de App_boothDongle corresponents a $APP_idDongle
            /****
             * 20220511 DONGLE MANA:
             * Es demana que a partir d'ara un intercanvi de dongle no afecti a la relacio dongle-id-serialnumber
             * Si es canvia un pc de una màquina o un dongle de màquina
             * Quan no coincideixi el que hi ha a la BD no actualitzem la BD, retornem l'id de la Bd perquè el PB el sobreescrigui al seu  arxiu PB/PBDC.dat
             */
            
            /***
             * 20220511 DONGLE MANA: no fem update ni nova relacio idBooth-idDongle
             */
//            $sql = "UPDATE App_boothDongle SET datetimeF=$APP_inTimeSerial WHERE idDongle=$APP_idDongle AND `datetimeF` IS NULL;";
//            $esOK = $APP_BdD->Execute($sql);
//            if(!$esOK) {
//                echo  "Error - Database update: $sql.";
//                return;
//            }
//            

            
//20130926 FINAL
            
            /***
             * 20220511 DONGLE MANA: no fem update ni nova relacio idBooth-idDongle
             */
//            $sql = "INSERT INTO App_boothDongle SET idBooth=$idb, idDongle=$APP_idDongle, datetimeS=$APP_inTimeSerial 
//            ON DUPLICATE KEY UPDATE idBooth=$idb, idDongle=$APP_idDongle, datetimeS=$APP_inTimeSerial;";//20130322
//            
//            
//            $esOK = $APP_BdD->Execute($sql);
//            if(!$esOK) {
//                echo  "Error - Database insert: $sql.";
//                return;
//
//            }
//            $APP_idBooth = $idb;
            
        }
//20130926    }
    
}
//20130424 INICI
        //$sql.= ", `version`=$ver";
if(isset($_REQUEST['vr'])){//només al Net_Start
    
    //20130510   NOTA: no se si seria millor comprovar l'actual owner
    
//20131002 INICI calcularem el desfase en minuts entre la hora del servidor i tact que afegeix PB_Net_Send
    //PENDENT!!!!!!!!!!!!!!!!
    
    
//20131003    $sql = "UPDATE App_booths SET owner=$APP_idRental, `version`=$ver WHERE idBooth=$APP_idBooth;";
//20140615 Tz!!!!    $sql = "UPDATE App_booths SET owner=$APP_idRental, `version`=$ver, `lastConn`=$APP_araTzTimeSerial,`lastConnZone`=$APP_araTzTimeSerial WHERE idBooth=$APP_idBooth;";//20131003 
//20160621    $sql = "UPDATE App_booths SET owner=$APP_idRental, `version`=$ver, `lastConn`=$APP_araTimeSerial,`lastConnZone`=$APP_araTzTimeSerial WHERE idBooth=$APP_idBooth;";//20140615 
//20170616    $sql = "UPDATE App_booths SET `version`=$ver, `lastConn`=$APP_araTimeSerial,`lastConnZone`=$APP_araTzTimeSerial WHERE idBooth=$APP_idBooth;";//20140615 
    $sql = "UPDATE App_booths SET `version`=$ver, `lastConn`=$APP_araTimeSerial,`lastConnZone`=$APP_araTzTimeSerial, `lastConnLocal`=$APP_inTimeSerial WHERE idBooth=$APP_idBooth;";//20170616
    
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        APP_fesLog("Error 20140708 - Database update: $sql");//20140708
        echo  "Error - Database update: $sql.";
        $APP_common_error = true;//20140708
        return;

    }

}
//20130424 FINAL
//20131003 INICI

else{
//20140615 Tz!!!!     $sql = "UPDATE App_booths SET `lastConn`=$APP_araTzTimeSerial,`lastConnZone`=$APP_araTzTimeSerial WHERE idBooth=$APP_idBooth;"; 
//20170616    $sql = "UPDATE App_booths SET `lastConn`=$APP_araTimeSerial,`lastConnZone`=$APP_araTzTimeSerial WHERE idBooth=$APP_idBooth;"; //20140615 Tz!!!! 

    $sql = "UPDATE App_booths SET `lastConn`=$APP_araTimeSerial,`lastConnZone`=$APP_araTzTimeSerial, `lastConnLocal`=$APP_inTimeSerial WHERE idBooth=$APP_idBooth;"; //20170616
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        APP_fesLog("Error 20131003 - Database update: $sql");
        echo  "Error - Database update: $sql.";
        $APP_common_error = true;//20140708
        return;

    }

}
//20131003 FINAL


//20150610????   if(!$IsPDnew){//PBnew
if(true){//20150610????   //PBnew


/////echo "TRACE 58 APP_idBooth: ".$APP_idBooth; return;



//20131002 INICI control de sessions diferent!!!!
//NOTA: quan un PB està alguns dies sense connectar-se a internet, quan ho fa envia tots els PN_Net_Start 
//i això provoca que es vagin creant sessions cada cop

//20150220noLog  APP_fesLog("Trace 20131002 - _REQUEST['vr']: {$_REQUEST['vr']} idb: $idb, APP_idBooth=$APP_idBooth, APP_inTimeSerial: $APP_inTimeSerial");

//20131007 if(isset($_REQUEST['vr'])){//només al Net_Start, crearem un registre
//20140605 if(isset($_REQUEST['vr']) || !$idb){//al Net_Start i si no hi ha param $_REQUEST['idb'], crearem un registre
if(isset($_REQUEST['vr'])){//20140605 //al Net_Start i si no hi ha param $_REQUEST['idb'], crearem un registre
    
    $sql = "INSERT INTO App_sessions SET idBooth=$APP_idBooth, idDongle=$APP_idDongle, start=$APP_inTimeSerial, last=$APP_inTimeSerial ;";
    $APP_startId = $APP_BdD->ExecuteInsert($sql);
    if(!$APP_startId) {
        APP_fesLog("Error - 51 - Database insert: $sql t=".$_REQUEST['t']);
        echo  "Error - 51 - Database insert: $sql.";
        $APP_common_error = true;//20140708
        return;

    }
    

}
else{//Ho farem comparant $APP_inTimeSerial amb App_sessions.start tenint en compte $idb

   
//20140708no no haver idb!!    $sql = "SELECT id FROM App_sessions WHERE idBooth=$idb AND start<$APP_inTimeSerial ORDER BY start DESC LIMIT 0,1;";
    $sql = "SELECT id FROM App_sessions WHERE idBooth=$APP_idBooth AND start<$APP_inTimeSerial ORDER BY start DESC LIMIT 0,1;";//20140708no no haver idb!! 
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
        APP_fesLog("Error - code 52 $sql t=".$_REQUEST['t']);
        
        
        echo  "Error - code 52 $sql";
        $APP_common_error = true;//20140708
        return;
    }
    $APP_startId = "";
    if($APP_BdD->FetchRs()){
        $APP_startId = $APP_BdD->GetField(1);
    }
    $APP_BdD->CloseRs();
    
    if($APP_startId){
    
        $sql = "UPDATE App_sessions SET last=$APP_inTimeSerial WHERE id=$APP_startId";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK) {
            APP_fesLog("Error - 53 - Database update: $sql.");
            echo  "Error - 53 - Database update: $sql.";
            $APP_common_error = true;//20140708
            return;
        }
    }
    else{//No hauria de passar
        APP_fesLog("Error - PB $idb sense App_sessions activa ($APP_inTimeSerial)!!! {$_SERVER['REQUEST_URI']}!!!!");
        $APP_startId = "-1";
//        $sql = "INSERT INTO App_sessions SET idBooth=$APP_idBooth, idDongle=$APP_idDongle, start=$APP_inTimeSerial, last=NULL ;";
//        $APP_startId = $APP_BdD->ExecuteInsert($sql);
//        if(!$APP_startId) {
//            APP_fesLog("Error - 54 - Database insert: $sql");
//            echo "Error - 54 - Database insert: $sql.";
//            return;
//
//        }
    }

}


//
//
//if(isset($_REQUEST['st'])){ $APP_startId = $_REQUEST['st'];} else {$APP_startId = "";}
////SELECT `id`, `idBooth`, `idDongle`, `start`, `last` FROM `App_sessions` WHERE 1
//
//
//// APP_fesLog("\nTRACE ". $_SERVER["SCRIPT_NAME"] . " idb: $APP_idBooth, st: $APP_startId, APP_inTimeSerial: $APP_inTimeSerial, APP_araTimeSerial: $APP_araTimeSerial");
//
//
//if(!$APP_startId){//nou registre a App_sessions
//    $sql = "INSERT INTO App_sessions SET idBooth=$APP_idBooth, idDongle=$APP_idDongle, start=$APP_inTimeSerial, last=$APP_inTimeSerial ;";
//    $APP_startId = $APP_BdD->ExecuteInsert($sql);
//    if(!$APP_startId) {
//        echo "Error - Database insert: $sql.";
//        return;
//
//    }
//    
//}
//else{//UPDATE
//    $sql = "UPDATE App_sessions SET last=$APP_inTimeSerial WHERE id= $APP_startId;";
//    $esOK = $APP_BdD->Execute($sql);
//    if(!$esOK) {
//        echo "Error - Database update: $sql.";
//        return;
//
//    }
//    
//}

//20131002 FINAL control de sessions diferent!!!!




//20130523 INICI
// si el mètode no és PB_Error caldrà desactivar qualsevol error dins les alertes
if(!isset($APP_common_alertErrorKO)){ //només la inicia PB_Error
    
    
//    echo "TRACE !isset($ APP_common_alertErrorKO";
    
    if(!isset($APP_common_isAlive)){ //201708

        
    include 'common/APP_common_alertErrorToOk.php';//sempre serà desactivar els erros actius (PENDENT DE FER
    
    if($APP_common_error) return;
    
    }//201708
    
}

//20130523 FINAL

//PBnew INICI
}
else{
    
}
//PBnew FINAL

include 'common/APP_common_alertOfflineToOk.php';//sempre serà desactivar
if($APP_common_error) return;

//NOTA, l'alerta offline s'haurà d'anar comprovant des del servidor amb cron !!!!!!!!!!!!!


//20140708  $APP_okResp = "OK#$APP_idBooth#$APP_startId";


  $APP_okResp = "OK#$APP_idBooth#$APP_startId#";//20140708


ob_end_clean();//20181109vic

?>
