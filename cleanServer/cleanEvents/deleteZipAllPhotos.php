<?php
/**Aquesta funcio elimina el arxiu comrpimit em totes les fotos (La copia de seguretat no, el que es descarreguen al myphotocode)*/
utils::log("---- Clean " . date("Y-m-d H:i:s") . " ---","logCronEvents");
$dd=  date('Ymd',strtotime('-2 months', strtotime($d)));
$dd6=  date('Ymd',strtotime('-6 months', strtotime($d)));

echo ">>> Searching ZipAllPhotos... \n";

//$sql = "SELECT start_date , id FROM events WHERE checked<$dd LIMIT 100";
$sql = "SELECT start_date , id FROM events WHERE checked<$d";
$CLD_CON->OpenRs($sql);
while($CLD_CON->FetchArray()){
    $id = $CLD_CON->GetArrayField("id");
    $startDate = $CLD_CON->GetArrayField("start_date");
    $folder = G_PATH . "events/".$startDate.$id;
       
    if(!is_dir($folder)){
        echo ">>> Event Folder Not Founded\n";
        echo ">>> ERROR 01\n";
    }
    else{
        $success = searchAndDeleteEventZip($folder , $id);
        if($success == 1){
            $CLD_CON->Execute("UPDATE SET checked = $d FROM events WHERE id = $id");
            echo ">>> Deleted, updated at db\n";
            echo ">>> SUCCESS \n";
        }
        elseif($success == 2){
//            echo ">>> All photos not downloaded yet \n";
//            echo ">>> Error 02\n";
        }
        else{
//            echo ">>> No contents founded\n";
//            echo ">>> Error 03\n";
        }
    }
    echo "\n";
}
?>
