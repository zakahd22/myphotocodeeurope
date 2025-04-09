<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();

$json = json_decode($_POST["data"], TRUE);

$ID = $json[0];
$cl = $json[1];
$eliminat = $json[2];
$cancel = $json[3];
//if ($json[4] != NULL) {
//    $ID = $json[4];
//}

$CLD_CON->OpenRs("SELECT id_event, collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");
    $collage = $CLD_CON->GetArrayField("collage");
}

if ($cancel == 1) {
    utils::log($cl, "logasd");
    $CLD_CON->OpenRs("UPDATE event_frame SET collage='$cl' WHERE id_event=$ID");
} else {
    if ($eliminat == 1) {
        utils::log('$eliminat', "logasd");
        $CLD_CON->OpenRs("UPDATE event_frame SET collage='' WHERE id_event=$ID");
    } else {
?>
                    <script>
//                        marcat($cl);
                    </script> 
        <?php
//
//        $pos = 1;

        if ($id_event == $ID) {
            $CLD_CON->OpenRs("UPDATE event_frame SET collage='$cl' WHERE id_event=$id_event");
        } else {
            $CLD_CON->OpenRs("INSERT INTO event_frame(id_event, collage) VALUES ('$ID', '$cl')");
        }
    }
}

if ($eliminat == 1) {
    $html = "any selected";
} else {
    $photo = str_replace("/", "_", $cl);

    $html = "You selected: $photo";
}

echo $html;
