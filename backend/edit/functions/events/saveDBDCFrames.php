<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();

$json = json_decode($_POST["data"], TRUE);

$ID = $json[0];
$fr = $json[1];
$eliminat = $json[2];
$cancel= $json[3];

$CLD_CON->OpenRs("SELECT id_event, frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $frame = $CLD_CON->GetArrayField("frame"); 
}
$frame = explode(";",$frame);
$llargada = sizeof($frame);
$pos = 1;
$existeix = false;

if($cancel == 1){
    $CLD_CON->OpenRs("UPDATE event_frame SET frame='$fr' WHERE id_event=$id_event"); 
}else{
    if($id_event == $ID){
        while($pos <= $llargada) {
            if($frame[$pos -1] == $fr){
                $existeix = true;
                $borra = $pos -1;
            }
            $pos = $pos + 1;
        }
        
        /*si la taula amb l'id de l'event existeix la modifica sino la crea*/
        if($existeix == false){       
            $frame[] = $fr;
            $cadena = implode(";", $frame);
            $CLD_CON->OpenRs("UPDATE event_frame SET frame='$cadena' WHERE id_event=$id_event"); 
        }
        else{
            if($eliminat == 1){
                unset($frame[$borra]);
                $cadena = implode(";", $frame);
                $CLD_CON->OpenRs("UPDATE event_frame SET frame='$cadena' WHERE id_event=$id_event");
            }
        }
    }
    else {
        $frame[] = $fr;
        $cadena = implode(";", $frame);
        $CLD_CON->OpenRs("INSERT INTO event_frame(id_event, frame) VALUES ('$ID', '$cadena')"); 
    }
}