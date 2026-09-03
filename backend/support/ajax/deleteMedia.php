<?php

include '../sessio.php';
include '../conexio.php';


$idMedia = $_POST['MID'];
$CLD_CON->OpenRs("SELECT ruta FROM SAT_media WHERE id=$idMedia");
if ($CLD_CON->FetchArray()) {
    $ruta = $CLD_CON->GetArrayField("ruta");
}
$CLD_CON->Execute("DELETE FROM SAT_media WHERE id=$idMedia");
if (file_exists($ruta)) {
    unlink($ruta);
}
?>
