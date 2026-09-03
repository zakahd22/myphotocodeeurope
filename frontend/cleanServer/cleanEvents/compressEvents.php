<?php

function get_IntDate($date){
    return intval(substr($date,0,4)."".substr($date,5,2)."".substr($date,8,2));
}

function notCompressible($d, $id, $CLD_CON2){
    //an event with files inside but anything are photos or videos.
    echo ">>> Event directory could not be compressed, not valid files inside \n";
    //echo ">>> Set as compressed anyway, no zip generated \n";
    //$CLD_CON2->Execute("UPDATE events SET compressed=$d WHERE id=$id");
    
    echo ">>> ERROR 00 \n";
}

function partialCompression($d, $id, $startDate, $event, $filelimit_compression, $CLD_CON2){
    //event directory contain more than 400 files, we have to remove only 400 photos
    echo ">>> Event directory contain more photos \n";
    echo ">>> compressed _______________/events/$startDate$id >> /events/compressed_events/".$id."_compressed.zip \n";
    $CLD_CON2->Execute("UPDATE events SET compressed=-$d WHERE id=$id");
    echo ">>> deleting {$filelimit_compression} files... _______/events/$startDate$id... \n";
    $deletedEvent=deleteIMG($event,$filelimit_compression);

    echo ">>> SUCCESS 01\n";
}

function notEventDir($d, $id, $CLD_CON2){
    /*
    event directory does not exist, remove event from the db? 
    It would be so much destructive, it wouldn't let any signal of the event anywhere 
    and if we want provide to somebody the event backup, 
    it would be impossible to find because we would need the id instanced in the db...
    
    Instead of that, it's necessary to avoid iterate always the same events, we have to filter them, 
    if not so unused events will finish compressed and it could NOT be. Events that had been compressed 
    are safe because they have a compressed field not null.
    */
    echo ">>> Event directory does not exist \n";
    $CLD_CON2->Execute("UPDATE events SET compressed=$d, private=1 WHERE id=$id");
    echo ">>> Event Set as compressed\n";
    echo ">>> SUCCESS 02\n";
}

function emptyEventDir($dir, $id, $state, $CLD_CON2, $d){
    if(!$state){
        //First Execution, event directory empty, not used in 6 months, we could delete it from the db?
        echo ">>> Event directory empty \n";
        $CLD_CON2->Execute("UPDATE events SET compressed=$d, private=1 WHERE id=$id");
        echo ">>> Event Set as compressed\n";
        echo ">>> SUCCESS 03\n";
    }
    else{
        //Second Execution, totally compressed, set as compressed
        echo ">>> Event directory totally compressed\n";
        $CLD_CON2->Execute("UPDATE events SET compressed=$d, private=1 WHERE id=$id");
        echo ">>> Event Set as compressed\n";
        echo ">>> SUCCESS 04 \n";
    }
}

function failedToCompress($dir, $id, $startDate, $state){
    if(!$state){
        /*
        First execution!, if failed to compress we can delete the zip and 
        we wouldn't loose any file.
        */

        echo ">>> FAILED to compress! ______/events/$startDate$id \n";
        echo ">>> deleting zip file... _____/events/compressed_events/".$id."_compressed.zip \n";
        $zipdeleted = deleteZip($dir, $id);
        if($zipdeleted){
            echo ">>> zip deleted ______________/events/compressed_events/".$id."_compressed.zip \n";
            echo ">>> ERROR 01 \n";
        }
        else{
            echo ">>> zip NOT deleted __________/events/compressed_events/".$id."_compressed.zip \n";
            echo ">>> ERROR 02 \n";
        }
    }
    else{
        //We will stay the db field marked as partially compressed, next execution will compress it.
        echo ">>> FAILED to compress! ______/events/$startDate$id \n";
        echo ">>> ERROR 03 \n";
    }
}

function successCompressed($d, $id, $startDate, $event, $filelimit_compression, $CLD_CON2){
    echo ">>> compressed _______________/events/$startDate$id >> /events/compressed_events/".$id."_compressed.zip \n";
    //$CLD_CON2->Execute("UPDATE events SET compressed=$d, private=1 WHERE id=$id");
    $CLD_CON2->Execute("UPDATE events SET compressed=$d, lastCompressed=$d WHERE id=$id");
    echo ">>> deleting... ______________/events/$startDate$id... \n";
    $deletedEvent=deleteIMG($event, $filelimit_compression);
    if($deletedEvent != -1){
        echo ">>> deleted {$deletedEvent} files_________/events/$startDate$id \n";
        echo ">>> SUCCESS 05\n";
    }
    else{
        //probably some subFolder of *-3D type, for the moment don't do anything
        echo ">>> Failed to delete, maybe some subFolder (*-3D) inside? Don't working well!!!!\n";
        echo ">>> ERROR 06 \n";
    }
}

$filelimit_compression = 200;

$event_timeout=  date('Y-m-d',strtotime('-20 days', strtotime($d)));
$intEvent_timeout = get_IntDate($event_timeout);
$dir = G_PATH . "events/";

//$sql = "SELECT id , start_date FROM events WHERE id = 1579";
//$sql = "SELECT id , start_date FROM events WHERE (CLD_date_lastPhoto < '$event_timeout' OR CLD_date_lastPhoto IS NULL) AND start_date < {$intEvent_timeout} AND (compressed IS NULL OR compressed < 0) LIMIT 100";
//20220627 events que han estat descomprimits recentment no els tornem a comprimir fins que ha passat 1 mes de lastCompressed
$sql = "SELECT id , start_date FROM events WHERE (CLD_date_lastPhoto < '$event_timeout' OR CLD_date_lastPhoto IS NULL) AND start_date < {$intEvent_timeout} AND (compressed IS NULL OR compressed < 0) AND (lastCompressed < {$intEvent_timeout} OR lastCompressed IS NULL) LIMIT 100";

echo $sql . "\n";

$CLD_CON->OpenRs($sql);
while($CLD_CON->FetchArray()){
    $id = $CLD_CON->GetArrayField("id");
    $startDate = $CLD_CON->GetArrayField("start_date");
    $compressed = $CLD_CON->GetArrayField("compressed");
    $event =  $dir.$startDate.$id;
    $state=0;
    //compelete event, not compressed yet
    echo ">>> compressing... ___________/events/$startDate$id... \n";
    if(!notZipFile($dir, $id)){
        echo ">>> partially compressed at.. ___________/events/compressed_events/{$id}_compressed.zip \n";
        $state = 1;
    }
    $event_compressed = compressEvent($event, $dir, $id, $filelimit_compression);

    if($event_compressed == 0){
        emptyEventDir($dir, $id, $state, $CLD_CON2, $d);
    }
    elseif($event_compressed == -1){
        notEventDir($d, $id, $CLD_CON2);
    }
    elseif($event_compressed == -2){
        failedToCompress($dir, $id, $startDate, $state);
    }
    elseif($event_compressed == -3){
        notCompressible($d, $id, $CLD_CON2);
    }
    elseif($event_compressed == $filelimit_compression){
        partialCompression($d, $id, $startDate, $event, $filelimit_compression, $CLD_CON2);
    }
    else{
        successCompressed($d, $id, $startDate, $event, $filelimit_compression, $CLD_CON2);
    }

    echo "\n";
}
?>