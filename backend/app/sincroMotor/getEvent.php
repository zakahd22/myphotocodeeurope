<?php
/*
 * Versió 9.0 27/10/2015
 Mètode: getEvent.php. retornarà dades de l'event, paràmetres:
 idE	numèric	id de l'Event, `events`.`id` bigint(4)
 * 

retornarà
JSON:
·	'idOwner', BdD  `events`.`rental_id` smallint(5)
·	'title', BdD `events`.`title` varchar(100)



Farem:
 * crear l'event amb dades de l'owner i manager. I codi de control
 * crear les carpetes i documents necessaris segons addNewEvent.php del Joan
 * crear un usb amb el model de photobooth
*/

$Mtr_script = "getEvent";
require("common.php"); 

//resposta
$resposta['status'] = 0;

$MTR_ok = true;      //PROVES ***************************************************************************

if(!$MTR_ok){
    $resposta['statusStr'] = $MTR_status;
    echo json_encode($resposta);
    return;
}

//paràmetres específics
if(isset($_REQUEST['idE'])){ $idEvent = $_REQUEST['idE'];} 
else {
    fesLog("Error - $Mtr_script, missing idE - Error01-");
    $resposta['statusStr'] = "Error01-idE";
    echo json_encode($resposta);
    return;
}


//torno al control per signatura
$signature = strtoupper(sha1($idEvent.$MTR_tact.$MTR_MtrControl));
if($signature != $MTR_sg){
    fesLog("Error - $Mtr_script, sg error local: $signature url:$MTR_sg  - Error02");
    $resposta['statusStr'] = "Error02";
    echo json_encode($resposta);
    return;
}

    $sql = "SELECT rental_id, title, CLD_SecurityCode FROM events WHERE id=$idEvent;";
    
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        fesLog("Error - $Mtr_script, Error02: $sql.");
        $resposta['statusStr'] = "Error02";
        echo json_encode($resposta);
        return;
    }
    if($APP_BdD->FetchRs()){
        $idOwner = $APP_BdD->GetField(1);
        $title = utf8_encode($APP_BdD->GetField(2)); 
        $code = $APP_BdD->GetField(3);
    }
    $APP_BdD->CloseRs();

//resposta
$resposta['idOwner'] = $idOwner;
$resposta['title'] = $title;
$resposta['code'] = $code;

$resposta['status'] = 1;
echo json_encode($resposta);

?>
