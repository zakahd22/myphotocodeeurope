<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$newSN = $_POST['newSN'];
$SNout = $_POST['SNout'];
$ID = $_POST['id'];
$componentID = $_POST['cmpID'];
$optional = $_POST['optional'];
$message = "";
$x = true;
$date = date("Y-m-d H:i:s");

$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth= $ID");
if ($CLD_CON->FetchArray()) {
    $snBooth = $CLD_CON->GetArrayField("serialnumber");
}

if (!empty($newSN)) {
    $CLD_CON->OpenRs("SELECT type , booth FROM CLD_components WHERE serialnumber='$newSN'");
    if ($CLD_CON->FetchArray()) {
        $type = $CLD_CON->GetArrayField("type");
        $booth = $CLD_CON->GetArrayField("booth");
        if ($type != $componentID) {
            $message = "Aquest $newSN no correspon aquest tipus de component A $type B $componentID";
            $x = false;
        }
        if (!empty($booth)) {
            $CLD_CON2->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth= $booth");
            if ($CLD_CON2->FetchArray()) {
                $snBooth2 = $CLD_CON2->GetArrayField("serialnumber");
            }
            $message = "El component em el numero de serie $newSN , segons el sistema ,  es troba en el photobooth $snBooth2";
            $x = false;
        }
    } else {
        $x = false;
        $message = "El component em  el numero de serie $newSN no existeix a la base de dades.";
    }
}

if ($x) {
    $y = true;
    if (!empty($SNout)) {
        if ($CLD_CON->Execute("UPDATE CLD_components SET booth=NULL WHERE serialnumber='$SNout'")) {
            $coment = "Removed from photobooth $snBooth";
            $CLD_CON->Execute("INSERT INTO CLD_historyComponents(comment , data , component_sn) VALUES('$coment' , '$date' , '$SNout')");
        } else {
            $message = "Hi hagut un error , siusplau torna-ho a provar";
            $y = false;
        }
    }
    if (!empty($newSN)) {
        if ($CLD_CON->Execute("UPDATE CLD_components SET booth=$ID WHERE serialnumber='$newSN'")) {
            $coment = "Added in the photobooth $snBooth";
            $CLD_CON->Execute("INSERT INTO CLD_historyComponents(comment , data , component_sn) VALUES('$coment' , '$date' , '$newSN')");
        } else {
            $message = "Hi hagut un error , siusplau torna-ho a provar";
            $y = false;
        }
    }
    if($y){
        echo "OK";
    }else{
        echo $message;
    }    
} else {
    echo $message;
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
