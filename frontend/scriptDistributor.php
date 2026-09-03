<?php
require_once './common/global.php';
require_once G_PATH . 'common/conexio.php';

$sql = "SELECT id, CLD_DistributorId FROM rentals";
$CLD_CON->OpenRs($sql);
if($CLD_CON->GetRsRows() > 0){
    while ($CLD_CON->FetchArray()) {
        $rental_id  = $CLD_CON->GetArrayField("id");
        $distributor  = $CLD_CON->GetArrayField("CLD_DistributorId");
        
        echo "Searching rental {$rental_id} in booths....... <br />";
        
        $sql = "UPDATE booths SET CLD_Distributor = {$distributor} WHERE rental_id = {$rental_id}";
        if($CLD_CON->Execute($sql) == 0){
            echo "Rental {$rental_id} has no dongle <br />";
            utils::log("TRACE scriptDistributor - Rental {$rental_id} has no dongle", "log");
        }
        else{
            echo "Updated {$rental_id} in booths! <br />";
        }
    }
}
else{
    echo "No rentals found <br />";
}