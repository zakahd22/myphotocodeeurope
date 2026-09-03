<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$boothID = $_POST['id'];
$dongle = $_POST['dongle'];
$date = date("Y-m-d G:i:s");
$CLD_CON->Execute("UPDATE App_boothDongle SET datetimeF='$date' WHERE (idBooth=$boothID OR idDongle=$dongle) AND datetimeF IS NULL");
if($CLD_CON->Execute("INSERT INTO App_boothDongle (idDongle , idBooth , datetimeS) VALUES ($dongle,$boothID,'$date')")){
    echo "OK";
}else{
    echo "ERROR";
}

/*
 * En aquest fitxer es canvian totes les sessions que tingues oberta aquest dongle
 * a tancades amb la data actual i sen obre una de nova amb el photobooth que s'hagi seleccionat
 * a la pagina de editar els dongles.
 */
?>
