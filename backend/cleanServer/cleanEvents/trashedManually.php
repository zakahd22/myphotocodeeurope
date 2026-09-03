<?php
$events = 0;
$already_removed_eventFolder = 0;
$removed_eventFolder = 0;
$removed_zip = 0;
$removed_usbFolder = 0;

$sql = "SELECT id , start_date FROM events WHERE (compressed IS NOT NULL AND compressed > 0) AND trashed IS NULL AND newServer = 0 AND CLD_date_lastPhoto <= '{$dateToTrash}' LIMIT 300";
//$sql = "SELECT id , start_date FROM events WHERE (compressed IS NOT NULL AND compressed > 0) AND trashed IS NULL AND newServer = 0 AND CLD_date_lastPhoto <= '2016-01-01'";
//$sql = "SELECT id , start_date FROM events WHERE compressed IS NOT NULL AND trashed IS NULL AND start_date<$dd AND id = 7306";
$CLD_CON->OpenRs($sql);

while($CLD_CON->FetchArray()){
    $events++;

    $id = $CLD_CON->GetArrayField("id");
    $startDate = $CLD_CON->GetArrayField("start_date");
    $folder =  G_PATH . "events/" . $startDate.$id;
    
    echo ">>> Copying {$id}_compressed.zip to trashed events... \n";
    if(file_exists(G_PATH . "events/compressed_events/" . $id . "_compressed.zip")){
        $trashed = eventToTrash(G_PATH, $id);
        if($trashed){
            echo ">>> Removing {$id} event... \n";

            echo ">>> Deleting event folder 'events/{$startDate}{$id}/'...\n";
            if(delete_directory($folder)){
                echo "SUCCESS \n"; $removed_eventFolder++;
            }
            else{
                echo "ERROR 01 - Could not remove folder\n";       
            }

            echo ">>> Deleting {$id}_compressed.zip... \n";
            if(unlink(G_PATH . "events/compressed_events/" . $id . "_compressed.zip")){
                $sql = "UPDATE events SET trashed=$d WHERE id=$id";
                $CLD_CON->Execute($sql);
                echo "SUCCESS \n"; $removed_zip++;
            }
            else{
                echo "ERROR 02 - Deleting {$id}_compressed.zip\n";       
            }

            echo ">>> Deleting usbs folder...\n";
            $sql = "SELECT * FROM usbs WHERE event_id=$id";
            $CLD_CON2->OpenRs($sql);
            while($CLD_CON2->FetchArray()){
                $usb_id = $CLD_CON2->GetArrayField("id");
                $usb_creationDate = $CLD_CON2->GetArrayField("creation_date");
                $usb_folder = G_PATH . "usbs/".$usb_creationDate.$usb_id;

                echo ">>> Deleting usb folder 'usbs/{$usb_creationDate}{$usb_id}'...\n";
                if(delete_directory($usb_folder)){
                    echo "SUCCESS \n"; $removed_usbFolder++;
                }
                else{
                    echo "ERROR 03 - Deleting usb folder\n";       
                }

                $CLD_CON3->Execute("UPDATE usbs SET available = 0 WHERE id = $usb_id");
                echo "USB - {$usb_id} marked as unavailable\n";
            }
        }
        else{
            echo "----------------------------------------------------\n";
            echo "FATAL ERROR - Could not copy {$id}_compressed.zip to trashed_events\n";
            echo "----------------------------------------------------\n";
        }
    }
    else{
        if(!file_exists($folder)){
            $sql = "UPDATE events SET trashed=$d WHERE id=$id";
            $CLD_CON->Execute($sql);
            $already_removed_eventFolder++;
        }
        else{
            echo "--------------------------------------------\n";
            echo "Event directory empty without compressed file...\n";
            echo "event = {$startDate}{$id}/\n";
            echo "--------------------------------------------\n";
            echo "removing event folder...";
            delete_directory($folder); $removed_eventFolder++;
        }
    }
}
    
echo "\n";
echo "TOTAL:\n";
echo "{$events} - Events to trash\n";
echo "{$already_removed_eventFolder} - Event Folders Already Removed\n";
echo "{$removed_eventFolder} - Event Folders Removed\n";
echo "{$removed_zip} - compressed ZIPs Removed\n";
echo "{$removed_usbFolder} - USBs Folders Removed\n";