<?php
$dd=  date('Ymd',strtotime('-12 months', strtotime($d)));
$sql = "SELECT id , start_date FROM events WHERE compressed IS NOT NULL AND trashed IS NULL AND start_date<$dd LIMIT 100";
//$sql = "SELECT id , start_date FROM events WHERE compressed IS NOT NULL AND trashed IS NULL AND start_date<$dd AND id = 7306";
$CLD_CON->OpenRs($sql);

while($CLD_CON->FetchArray()){
    $id = $CLD_CON->GetArrayField("id");
    $startDate = $CLD_CON->GetArrayField("start_date");
    $folder =  G_PATH . "events/" . $startDate.$id;
    
    //writeOnLog("logtoTrash.txt", "$id4 $startDate4");
    echo ">>> Copying {$id}_compressed.zip to trashed events... \n";
    $trashed = eventToTrash(G_PATH, $id);
    if($trashed){
        $sql = "UPDATE events SET trashed=$d WHERE id=$id";
        $CLD_CON->Execute($sql);
        
        echo ">>> Deleting {$id}_compressed.zip... \n";
        eliminarDir($folder);
        
        echo ">>> Deleting event folder \n";
        unlink(G_PATH . "events/compressed_events/".$id."_compressed.zip");

        echo ">>> Deleting usbs folder... \n";
        $sql = "SELECT * FROM usbs WHERE event_id=$id";
        $CLD_CON2->OpenRs($sql);
        while($CLD_CON2->FetchArray()){
            $usb_id = $CLD_CON2->GetArrayField("id");
            $usb_creationDate = $CLD_CON2->GetArrayField("creation_date");
            $usb_folder = G_PATH . "/usbs/".$usb_creationDate.$usb_id;
            
            echo "Deleting usb... ___________/usbs/{$usb_creationDate}{$usb_id} \n";
            delete_directory($usb_folder);
            $CLD_CON3->Execute("DELETE FROM usbs WHERE id=$usb_id");
        }
        echo "SUCCESS \n";
    }
    else{
        echo "ERROR 01 \n";
    }
    echo "\n";
}

/*Pasa els events a la carpete trashed_events i marcar-los com a eliminats
$dd=  date('Ymd',strtotime('-12 months', strtotime($d)));
$CLD_CON->OpenRs("SELECT id , start_date FROM events WHERE compressed IS NOT NULL AND trashed IS NULL AND start_date<$dd LIMIT 300");
while($CLD_CON->FetchArray()){
    $id4 = $CLD_CON->GetArrayField("id");
    $startDate4 = $CLD_CON->GetArrayField("start_date");
    $folder4 =  G_PATH . "events/" . $startDate4 . $id4;
    writeOnLog("logtoTrash.txt", "$id4 $startDate4");
    eventToTrash($folder4, $id4);
    $CLD_CON2->Execute("UPDATE events SET trashed=$d WHERE id=$id4");
}

*/
/*Elimina la carpeta del event i tot el seu contingut , elimina el compressed Zip
$CLD_CON->OpenRs("SELECT id , start_date FROM events WHERE trashed=$d");
while($CLD_CON->FetchArray()){
    $id5 = $CLD_CON->GetArrayField("id");    
    $startDate5 = $CLD_CON->GetArrayField("start_date");
    $folder3 =  G_PATH . "events/" . $startDate5 . $id5;
    eliminarDir($folder3);
    unlink(G_PATH . "events/compressed_events/" . $id5 . "_compressed.zip");
    writeOnLog("logDeleteEvent.txt", "$id5 $startDate5 - $startDate5$id5");  
    $CLD_CON2->OpenRs("SELECT * FROM usbs WHERE event_id=$id5");
    while($CLD_CON2->FetchArray()){
        $id = $CLD_CON2->GetArrayField("id");
        $creationDate = $CLD_CON2->GetArrayField("creation_date");
        $folder = G_PATH . "usbs/".$creationDate.$id;
        delete_directory($folder);
        $CLD_CON3->Execute("DELETE FROM usbs WHERE id=$id");
    }
}*/
?>
