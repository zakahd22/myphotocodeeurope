<?php
$dd= date('Ymd',strtotime('-6 months', strtotime($d)));
//writeOnLog("logCronUSB.txt" ,  "---- Clean " . date("Y-m-d H:i:s") . " ---");
//not necessary to wait to delete, is regenerated if not founded when download
$i=0;
echo ">>> Searching USB zip... \n";

//$sql = "SELECT creation_date , id FROM usbs WHERE creation_date < $dd";
$sql = "SELECT creation_date , id FROM usbs";
$CLD_CON->OpenRs($sql);
$CLD_CON2 = clone($CLD_CON);
while($CLD_CON->FetchArray()){
    $id = $CLD_CON->GetArrayField("id");
    $creationDate = $CLD_CON->GetArrayField("creation_date");
    $folder = G_PATH . "usbs/" . $creationDate . $id;
    //$folder = "../usbs/" . $creationDate . $id;
    
    if(is_dir($folder)){
        $founded=searchAndDeleteUSBZip($folder);
        if($founded == 2){
            $i++;
            echo ">>> usbs/{$creationDate}{$id}/save-in-usb.zip\n";
            echo ">>> Deleted\n";
        }
        else if($founded == 1){
//          echo ">>> USB zip /usbs/{$creationDate}{$id}/  not downloaded yet\n";
//          echo ">>> ERROR 03\n";
        }
        else{
//            echo ">>> No contents founded \n";
//            echo ">>> ERROR 02\n";
        }
    }
}

echo "Deleted $i save-in-usb.zip\n";
?>


