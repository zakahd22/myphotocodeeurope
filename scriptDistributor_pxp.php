<?php
require_once './common/global.php';
require_once G_PATH . 'common/conexio.php';

$sql = "SELECT id, CLD_Distributor, rental_id FROM booths";
    
$CLD_CON->OpenRs($sql);

if($CLD_CON->GetRsRows() > 0){
    $i = 0;
    while ($CLD_CON->FetchArray()) {
        $dongle_id  = $CLD_CON->GetArrayField("id");
        $distributor  = $CLD_CON->GetArrayField("CLD_Distributor");
        $owner  = $CLD_CON->GetArrayField("rental_id");

        echo "Searching dongle {$dongle_id} in booths....... <br />";

		$sql = "UPDATE Pay_print_order SET CLD_Distributor = {$distributor}, idOwner = {$owner} WHERE idDongle = {$dongle_id}";
		if($CLD_CON->Execute($sql) == 0){
	        echo "Dongle {$dongle_id} has no orders";
	    }
	    else{
            echo "Updated {$dongle_id} in Pay_print_order! <br />";
        }
	}
}
else{
    echo "No dongles found <br />";
}