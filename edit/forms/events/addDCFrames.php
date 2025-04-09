<?php
$title = "DC default frames";

$content = "";
$buttones = "";


$content .= "<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>";

$content .= "<script src='sections/events/resources/js/printPhotos.js'/>";
$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Choose the frame you want from below:";
$content .= "</div>";
$content .= "<select id='dFrameSel' class='popup-input-large' style='width: 200px;' event='$ID'>";

$CLD_CON->OpenRs("SELECT id_event, frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $frame = $CLD_CON->GetArrayField("frame"); 
}

$content .= "<option value=0>None</option>";
$CLD_CON->OpenRs("SELECT id , title FROM frames");
while ($CLD_CON->FetchArray()) {
    $frame_id = $CLD_CON->GetArrayField("id");
    $frame_title = $CLD_CON->GetArrayField("title");
    $frame_title = str_replace("_", " ", $frame_title);
    $content .= "<option value='$frame_id'>$frame_title</option>";  
}


$content .= "</select>";
$photo = explode(";",$frame);

$Ncustom = preg_grep("/custom.*/", $photo);
$Ncustom = count($Ncustom)+1; // conta els custom i suma un ja que el primer valor de l'array esta vuit
$photo = count($photo)-$Ncustom;
$content .= <<<HTML
    <div id="mainDcFrames">
        <div id="marcat"> You selected: $photo </div>
        <div id='contentDcFrames' style='margin-top:10px'>
        </div>
    </div>
    <input type='hidden' id='dFrame'>    
HTML;
$fr = implode(";",$frame);
utils::log($frame, "logasd");
$buttons .= "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save({$ID}, \"{$frame}\"); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$CLD_CON->OpenRs("SELECT id_event, frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $frame = $CLD_CON->GetArrayField("frame"); 
}

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);

//onclick='saveDCFrames($ID); hidePopupv2();'