<?php
/*
 * Versió 1.0 22/03/2016
 * Script paralel a https://www.control.alquilafotomaton.es/mypc_service.php
 * però els paràmetres comuns seràn aquests
  
idm	numèric	identificació del motor (ho preparem per si hi ha més d'un: USA, ES, etc.)
sg	cadena alfanumèrica	signatura de control
tact	AAAAMMDDHHMMSS	temps d'enviament  en format AAAAMMDDHHMMSS segons hora local del motor

Amb el paràmetre p3 es defineix quina de les funcions s’executa. No hi harà ni p1 ni p2.


Nom de la funció que s’executara

Resposta, serà JSON, part genèrica:
• 'status', 0 error, 1 sense errors
• 'statusStr', text de l'error si status = 0

*/

$Mtr_script = "motor_service";
require("common.php"); 

//resposta
$resposta['status'] = 0;

     //PROVES  $MTR_ok = true;   ***************************************************************************

if(!$MTR_ok){
    $resposta['statusStr'] = $MTR_status;
    echo json_encode($resposta);
    return;
}

$function   = $_REQUEST["p3"];
$signature = strtoupper(sha1($MTR_tact . $function . $_REQUEST["p4"] . $_REQUEST["p5"]. $_REQUEST["p6"]. $MTR_MtrControl));

if($MTR_sg != $signature){         
//    utils::log("La signatura no coincideix", G_PATH. "log/log_mypc_conect");
//    utils::log("Local: {$sg_local}", G_PATH. "log/log_mypc_conect");
//    utils::log("Externa: {$sg}", G_PATH. "log/log_mypc_conect");
    
    fesLog("Error - $Mtr_script, sg error local: $signature url:$MTR_sg  - Error01");
    
    $resposta['statusStr'] = "Error 01";
    echo json_encode($resposta);
    return;
}


switch ($function) {
    
    case "ChgPbsName":
        if(isset($_REQUEST['p4'])){ $mypc_id_pb = $_REQUEST['p4'];} 
        else {
            fesLog("Error - $Mtr_script, missing p4 id_pb - Error01-");
            $resposta['statusStr'] = "Error02-p4";
            echo json_encode($resposta);
            return;
        }
        if(isset($_REQUEST['p5'])){ $pbs_name = $_REQUEST['p5'];} 
        else {
            fesLog("Error - $Mtr_script, missing p5 pbs_name - Error01-");
            $resposta['statusStr'] = "Error02-p5";
            echo json_encode($resposta);
            return;
        }
        $sql = "UPDATE App_booths SET name='".str_replace("'","",$pbs_name)."' WHERE idBooth=$mypc_id_pb; ";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK){
            fesLog("Error - $Mtr_script, Error03_sql: $sql.");
            $resposta['statusStr'] = "Error03_sql";
            echo json_encode($resposta);
            return;
        }        
        break;
        

    case "ChgAletEmail":
        if(isset($_REQUEST['p4'])){ $mypc_id_owner = $_REQUEST['p4'];} 
        else {
            fesLog("Error - $Mtr_script, missing p4 mypc_id_owner - Error01-");
            $resposta['statusStr'] = "Error02-p4";
            echo json_encode($resposta);
            return;
        }
        if(isset($_REQUEST['p5'])){ $mail = $_REQUEST['p5'];} 
        else {
            fesLog("Error - $Mtr_script, missing p5 mail - Error01-");
            $resposta['statusStr'] = "Error02-p5";
            echo json_encode($resposta);
            return;
        }
        $sql = "UPDATE rentals SET App_email='".str_replace("'","",$mail)."',`ValidatedAlertEmail`=1 WHERE id=$mypc_id_owner; ";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK){
            fesLog("Error - $Mtr_script, Error03_sql: $sql.");
            $resposta['statusStr'] = "Error03_sql";
            echo json_encode($resposta);
            return;
        }        
        break;
 
    case "ChgOwnPasswd":
        if(isset($_REQUEST['p4'])){ $mypc_id_owner = $_REQUEST['p4'];} 
        else {
            fesLog("Error - $Mtr_script, missing p4 mypc_id_owner - Error01-");
            $resposta['statusStr'] = "Error02-p4";
            echo json_encode($resposta);
            return;
        }
        if(isset($_REQUEST['p5'])){ $password = $_REQUEST['p5'];} 
        else {
            fesLog("Error - $Mtr_script, missing p5 password - Error01-");
            $resposta['statusStr'] = "Error02-p5";
            echo json_encode($resposta);
            return;
        }
        $sql = "UPDATE rentals SET password='".str_replace("'","",$mail)."' WHERE id=$mypc_id_owner; ";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK){
            fesLog("Error - $Mtr_script, Error03_sql: $sql.");
            $resposta['statusStr'] = "Error03_sql";
            echo json_encode($resposta);
            return;
        }        
        
        break;
}
//resposta
$resposta['status'] = 1;
echo json_encode($resposta);

?>
