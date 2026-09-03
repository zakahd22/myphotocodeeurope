<?php
require_once "../../common/global.php";
require_once '../../common/conexio.php';

//ob_start();
$now = new DateTime();
$now = $now->format('Y-m-d H:i:s');

utils::log("====== Inici del Cicle ======", "logGestor");
//$versioPB = 30;
//$codiFoto= 'PAROGADNUD1';
//$metode = 0;
//$contacte = 'alex@dc-image.com';
//$idb = 1455;


utils::log("codi de la foto: $codiFoto", "logGestor");
$sql = "SELECT `code`, `contact` FROM `gestor` WHERE `code` = '$codiFoto' and `contact` = '$contacte'";

$CLD_CON->OpenRs($sql);
while ($CLD_CON->FetchArray()) {
    $code_exist = $CLD_CON->GetArrayField("code");
    $contact_exist = $CLD_CON->GetArrayField("contact");
}  

if(!isset($code_exist) && !isset($contact_exist)){
    $CLD_CON->Execute("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`, `idb`) VALUES ('$codiFoto', '$metode', '$contacte', '$now', 1, '$versioPB', '$idb')");    
    utils::log("insert fet", "logGestor");
}
    
//}

utils::log("       ", "logGestor");
utils::log("====== Fi del Cicle ======", "logGestor");
utils::log("       ", "logGestor");
//ob_get_contents();
//ob_end_clean();
//http://localhost/myphotocode/app/photobooth/PBnew_Share.php?pb=30&dongle=2091411260&idmaq=F7HR&t=20180711125832&idb=7550&code=PAROGADNUD&mtd=0&dt=alex@dc-image.com