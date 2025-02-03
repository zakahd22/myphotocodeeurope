<?php
/*******
 * Informació enviada pels PBs en la gestió de dispositius
 * 2022-05-13 Eloi Britta 3.2
 * PBs basats en Britta.
 * Sempre que, en interaccionar amb un disposittiu, hagi de fer més d’un intent enviará informació a MyPC.
 * 
 * Paràmetres
 * id, id del PB numeric id assignat al PB si es null no s'accepta
 * dongle, id del dongle definit per Sentinel
 * t, timestamp AAAAMMDDHHMMSS
 * device, quin dispositiu  (1: controlBoard, 2: camera, 3: printer)
 * attempts, nombre d’intents d’accés
 * resp, llista de respostes separades per ‘|’
 * action, acció del PB (0: res, 1: fastrestart, 2: restart, 3: reboot)
 * idInfo, camp idInfo de la taula App_info 
 * 
 * Guardem també: idDM, when, pbs_time, db_time

 */
require("common.php");

if($APP_common_error){
    APP_fesLog("Error20161107 in PBnew_Error($idb): $APP_common_error. APP_dongleOK: $APP_dongleOK");
    return;
}

if(!$APP_dongleOK) return;

$device = "";
$attempts = "";
$resp = "";
$action = "";
$idInfo = "";
$okMD = "";

$sqlParams="";

//20250124DeviceMgt if(isset($_REQUEST['device'])){     $device = $_REQUEST['device'];          $sqlParams.=",device='$device' ";}
if(isset($_REQUEST['device'])){     $device = $_REQUEST['device'];          $sqlParams.=",device='".str_replace("'","''",$device)."' ";}//20250124DeviceMgt 
//20250124DeviceMgt if(isset($_REQUEST['modeldev'])){     $model = $_REQUEST['modeldev'];          $sqlParams.=",model='$model' ";}
if(isset($_REQUEST['modeldev'])){     $model = $_REQUEST['modeldev'];          $sqlParams.=",model='".str_replace("'","''",$model)."' ";}//20250124DeviceMgt 
//20250124DeviceMgt if(isset($_REQUEST['error'])){     $error = $_REQUEST['error'];          $sqlParams.=",error='$error' ";}
if(isset($_REQUEST['error'])){     $error = $_REQUEST['error'];          $sqlParams.=",error='".str_replace("'","''",$error)."' ";}//20250124DeviceMgt 

if(isset($_REQUEST['attempts'])){   $attempts = $_REQUEST['attempts'];      $sqlParams.=",attempts='$attempts' ";}
if(isset($_REQUEST['resp'])){       $resp = $_REQUEST['resp'];              $sqlParams.=",resp='$resp' ";}
if(isset($_REQUEST['action'])){     $action = $_REQUEST['action'];          $sqlParams.=",action='$action' ";}
//if(isset($_REQUEST['idInfo'])){     $idInfo = $_REQUEST['idInfo'];          $sqlParams.=",idInfo='$idInfo' ";}
if(isset($_REQUEST['ok'])){         $okMD = $_REQUEST['ok'];                $sqlParams.=",ok='$okMD' ";}

$sql = "INSERT INTO  `App_infoDeviceMgt` SET `when`=$APP_inTimeSerial,`idBooth`=$APP_idBooth,`idDongle`=$APP_idDongle ";
$sql.= $sqlParams;
       
        

if($APP_tactSql) $sql.=",pbs_time=$APP_tactSql "; //20170627tact
$sql.=",db_time=$APP_araTimeSerial";//20170627dbtime


//error_log( "TO_DELETE sql: $sql" );

        $esOK = $APP_BdD->ExecuteInsert($sql);
        
        if(!$esOK) {
            if($APP_BdD->errno != 1062){//202201dup
            echo "Error - Error - Database insert: $sql.";
            APP_fesLogDebbug("Error 20170629-202201 $APP_BdD->errno,$APP_BdD->error   sql: $sql","logDebug20170629-202201pb");
            return;
            }//202201dup
        }


/**
 * TODO: Enviem mail a DC. erlog@digital-centre.com
 */    



echo $APP_okResp;   



?>
