<?php

function checkIfEventExist($id){
    global $CLD_CON;
    
    $exist = false;
    $sql = "
        SELECT 1
        FROM events
        WHERE id = {$id}";
    
    $CLD_CON->OpenRs($sql);

    if($CLD_CON->FetchArray()){
        $exist = true;
    }
    
    return $exist;
}

function has_photos($id){
    global $CLD_CON;
    
    $hasPhotos = false;
    $sql = "
        SELECT count(*) AS counter
        FROM photos
        WHERE event_id = {$id}
        GROUP BY event_id
    ";
    
    $CLD_CON->OpenRs($sql);

    if($CLD_CON->FetchArray()){
        $hasPhotos = true;
    }
    
    return $hasPhotos;
}

function getRental_id($id){
    global $CLD_CON;
    
    $rental = false;
    $sql = "
        SELECT rental_id FROM booths 
        INNER JOIN (
            SELECT photos.booth_id AS booth_id
            FROM photos
            WHERE photos.event_id = {$id}
            GROUP BY photos.event_id
        ) AS photo
        ON photo.booth_id = booths.id
    ";
    
    $CLD_CON->OpenRs($sql);

    if($CLD_CON->FetchArray()){
        $rental = $CLD_CON->GetArrayField("rental_id");
    }
    
    return $rental;
}

function getLastPhoto($id){
    global $CLD_CON;
    
    $lastPhoto = false;
    $sql = "
       SELECT max( Appusr_datetime ) AS lastPhoto
        FROM photos
        WHERE event_id = {$id}
        GROUP BY event_id
    ";
    
    $CLD_CON->OpenRs($sql);

    if($CLD_CON->FetchArray()){
        $lastPhoto = $CLD_CON->GetArrayField("lastPhoto");
    }
    
    return $lastPhoto;
}

function createEvent($id, $startDate, $rental_id, $lastPhoto){
    global $CLD_CON;
    
    $event = false;
    $sql = "
        INSERT INTO events (id, rental_id, start_date, title, background_id, CLD_banner, CLD_banner_URL, private, autocreated, ftp_folder_id, available, CLD_invitedName, CLD_invitedEmail, CLD_SecurityCode, CLD_eventManegerId, CLD_date_lastPhoto, hashtag, checked, compressed, trashed) 
        VALUES ({$id}, {$rental_id}, '{$startDate}', 'Restored - {$startDate}', '0', '0', NULL, '1', '1', '1', '1', NULL, NULL, NULL, NULL, '{$lastPhoto}', NULL, NULL, NULL, NULL);
    ";
    
    $event = $CLD_CON->ExecuteInsert($sql);
    
    return $event;
}

$toremove = 0;
$removed = 0;
$failed_remove = 0;

$torestore = 0;
$restored = 0;
$failed_restore = 0;
$eventFolder = G_PATH . "events/";

if (is_dir($eventFolder)){
    $array_EventsFolders = scandir($eventFolder);
    foreach($array_EventsFolders as $event){
//        echo $event . "\n";
        if(($event != '.') && ($event != '..')){
            if(($event != 'compressed_events') && ($event != 'trashed_events')){
                if(is_dir($eventFolder.$event)){
                    $startDate = substr($event, 0, 8);
                    $event_id = substr($event, 8);
                    
                    if(is_numeric($event)){
                        if(!checkIfEventExist($event_id)){
                            echo ">>> INTEGRITY ERROR!! Event {$event} -- id = {$event_id} does not exist!\n";
                            if(has_photos($event_id)){
                                $torestore++;
                                echo ">>> Restoring DB... ";
                                if($rental_id = getRental_id($event_id)){
                                    echo "rental = {$rental_id}; ";
                                    if($lastPhoto = getLastPhoto($event_id)){
                                        echo "lastPhoto = {$lastPhoto}\n";
                                        if(createEvent($event_id, $startDate, $rental_id, $lastPhoto)){
                                            echo "Succesfull!! \n";
                                            $restored++;
                                        }
                                    }
                                    else{
                                        echo "\n";
                                        echo ">>> ERROR: Failed to restore, no lastPhoto Date\n";
                                    }
                                }
                                else{
                                    echo "\n";
                                    echo ">>> ERROR: Failed to restore, no rental_id\n";
                                    echo ">>> removing from DB\n";
                                    echo ">>> To remove\n";
                                    $toremove++;
                                    if(delete_directory($eventFolder.$event)){
                                        echo "FIXED\n";
                                        $removed++;
                                    }
                                    else{
                                        echo "ERROR\n";
                                    }
                                }
                            }
                            else{
                                echo ">>> To remove\n";
                                $toremove++;
                                if(delete_directory($eventFolder.$event)){
                                    echo "FIXED\n";
                                    $removed++;
                                }
                                else{
                                    echo "ERROR\n";
                                }
                            }
                        }
                    }
                    else{
                        echo ">>> Strange event folder {$event}?... Manual Repair!\n";
                    }
                }
            }
        }
//        }
    }
    echo "\n";
}
else{
    echo "ERROR 01 \n";
}

echo ">>> TOTAL Removed = {$toremove}\n";
$failed_remove = $toremove - $removed;
echo ">>> TOTAL Failed to Removed = {$failed_remove}\n";

echo ">>> TOTAL Restored = {$restored}\n";
$failed_restore = $torestore - $restored;
echo ">>> TOTAL Failed to Restored = {$failed_restore}\n";