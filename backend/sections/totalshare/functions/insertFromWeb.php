<?php
require_once "../../../common/global.php";
require_once '../../../common/conexio.php';

require_once "copiaHistoric.php";

// si ve de la web $_POST["data"] estará ple, i voldra dir que les variables estan buides i les hem de omplir amb el post.
// si ve desde una maquina, $_POST['data'] estará buit, y les variables estarán plenes per PBnew_Share.php, no les volem sobreescriure
$now = new DateTime();
$now = $now->format('Y-m-d H:i:s');

if (isset($_POST["data"])) {
    $json = json_decode($_POST["data"], TRUE);
    $codiFoto = $json[0];
    $metode = $json[1];
    $contacte = $json[2];
    $web = $json[3];
    $pref = $json[4];

    // si hi ha $pref vol dir que es un telefon, l'ajuntem amb el contacte.   
    if ($pref) {
        $contacte = $pref . $contacte;
    }
}
if ($metode == 1) {
//    $contacte = $pref . $contacte;
    $mini = substr($contacte, 1);
    $contacte = "+" . ltrim($mini, "0 ");
}

if (isset($contacte)) {
    $CLD_CON->Execute("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`) VALUES ('$codiFoto', '$metode', '$contacte', '$now', 2, 'WEB')");
}
