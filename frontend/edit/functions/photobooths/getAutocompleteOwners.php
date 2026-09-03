<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$result = "Error";

$CLD_CON->OpenRs(
   "SELECT rentals.id AS id, rentals.name AS name
    FROM rentals
    ORDER BY rentals.name"
);

if($CLD_CON->GetRsRows() > 0){
    $i = 0;
    $result = array();
    while ($CLD_CON->FetchArray()) {
        $result[$i]['value']  = $CLD_CON->GetArrayField("id");
        $result[$i]['label']  = $CLD_CON->GetArrayField("name");
        $i++;
    }
}

echo json_encode($result);