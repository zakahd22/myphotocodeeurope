<?php
$title = "DC default collages";

$content = "";
$buttones = "";
$CLD_CON->OpenRs("SELECT id_event, collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $collage = $CLD_CON->GetArrayField("collage"); 
}

$content .= "<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>";
$content .= "<script src='sections/events/resources/js/printCollage.js'/>";
$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Choose the collage you want from below:";
$content .= "</div>";
$content .= "<select id='dCollagesSel' class='popup-input-large' style='width: 200px;' event='$ID'>";
$content .= "<option value=0>None</option>";
$CLD_CON->OpenRs("SELECT id , title FROM collages");
while ($CLD_CON->FetchArray()) {
    $collage_id = $CLD_CON->GetArrayField("id");
    $collage_title = $CLD_CON->GetArrayField("title");
    $collage_title = str_replace("_", " ", $collage_title);
    $content .= "<option value='$collage_id'>$collage_title</option>";  
}

$content .= "</select>";
$photo = str_replace("/", "_", $collage);
$content .= <<<HTML
    <div id="mainDcCollages">
        <div id="marcat"> You selected: $photo </div>
        <div id='contentDcFrames' style='margin-top:10px'>
        </div>
    </div>
    <input type='hidden' id='dCollage'>    
HTML;


$buttons .= "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save_collage({$ID}); hidePopupv2();'>";
$buttons .= "<input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='cancel_collage({$ID}, \"{$collage}\"); hidePopupv2();'>";



$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);