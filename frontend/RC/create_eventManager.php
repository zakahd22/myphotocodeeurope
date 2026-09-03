<?php
include '../common/global.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_GET['ev'];
$inEmail = $_GET["email"];
$inName = $_GET['nm'];

function generarCodigo($longitud) {
    $key = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
    $max = strlen($pattern) - 1;
    for ($i = 0; $i < $longitud; $i++)
        $key .= $pattern{mt_rand(0, $max)};
    return $key;
}

$securityCode = strtoupper(generarCodigo(10));
$password = strtoupper(generarCodigo(10));

$nm = explode(" ", $inName, 1);
$id_eventM = $CLD_CON->ExecuteInsert("INSERT INTO CLD_EventsManegers (name ,surname,email) VALUES ('" . $nm[0] . "', '" . $nm[1] . "', '$inEmail')");
$CLD_CON->ExecuteInsert("INSERT INTO CLD_Login ('$inEmail' , '$password' , $id_eventM , 5)");
$CLD_CON->Execute("UPDATE events SET CLD_invitedName='$inName' , CLD_invitedEmail='$inEmail' , CLD_SecurityCode='$securityCode' , CLD_eventManegerId=$id_eventM WHERE id=$ID");

echo $id_eventM;
?>
