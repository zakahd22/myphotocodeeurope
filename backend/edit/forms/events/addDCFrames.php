<?php
$title = "DC default frames";

$content = "";
$buttons = "";

// Load CSS + JS properly
$content .= "<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>";
$content .= "<script src='sections/events/resources/js/printPhotos.js'></script>";

// Header text
$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Choose the frame you want from below:";
$content .= "</div>";

// Dropdown selector
$content .= "<select id='dFrameSel' class='popup-input-large' style='width: 200px;' event='$ID'>";

// Load existing frame selection for this event
$frame = "";  // Default safe value

$CLD_CON->OpenRs("SELECT frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $frame = $CLD_CON->GetArrayField("frame") ?? "";
}

// Dropdown first option
$content .= "<option value='0'>None</option>";

// Load all available frames
$CLD_CON->OpenRs("SELECT id, title FROM frames");
while ($CLD_CON->FetchArray()) {
    $frame_id = $CLD_CON->GetArrayField("id");
    $frame_title = $CLD_CON->GetArrayField("title");
    $frame_title = str_replace("_", " ", $frame_title);

    $content .= "<option value='$frame_id'>$frame_title</option>";
}

$content .= "</select>";

// ---------------------------------------
// PROCESS FRAME LIST
// ---------------------------------------

$photoArray = [];

if (!empty($frame)) {
    $photoArray = explode(";", $frame);
}

// Count custom frames
$customFrames = preg_grep("/custom.*/", $photoArray);
$customCount = count($customFrames);

// Count normal frames
$normalCount = count($photoArray) - $customCount;
if ($normalCount < 0) $normalCount = 0;

// Display selected count
$content .= <<<HTML
    <div id="mainDcFrames">
        <div id="marcat"> You selected: $normalCount </div>
        <div id='contentDcFrames' style='margin-top:10px'></div>
    </div>
    <input type='hidden' id='dFrame'>
HTML;

// Log for debugging
utils::log("Loaded frame: " . $frame, "logasd");

// ---------------------------------------
// BUTTONS
// ---------------------------------------
$buttons .= "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save({$ID}, \"{$frame}\"); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

// Package JSON result
$array_result = array(
    'title'   => $title,
    'content' => $content,
    'buttons' => $buttons
);

echo json_encode($array_result);
