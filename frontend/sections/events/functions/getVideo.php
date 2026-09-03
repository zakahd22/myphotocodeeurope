<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$imageName = $_POST['code'];

$trozos = explode(".", $imageName);
$extension = $trozos[1];
$code = $trozos[0];

$CLD_CON->OpenRs("SELECT e.start_date , e.id FROM photos p LEFT JOIN events e ON p.event_id = e.id WHERE p.code='$code'");
if ($CLD_CON->FetchArray()) {
    $eventID = $CLD_CON->GetArrayField("id");
    $date = $CLD_CON->GetArrayField("start_date");
    $imgPath = "events/" . $date . $eventID . "/" . $trozos[0];

    if (file_exists(G_PATH. $imgPath . ".mp4")) {
        $extension = ".mp4";
    }
    elseif (file_exists(G_PATH. $imgPath . ".wmv")) {
        $extension = ".wmv";
    }
    
    $image = $imgPath . $extension;
}

echo "<video src='$image' width='320' height='200' class='videoPOP' id='vid1' controls preload></video>";




/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
    $(document).keyup(function(e) {
        if (e.keyCode == 27) { 
            $("#vid1").stop();
            //closePhoto();
        }   // esc
    });
</script>    