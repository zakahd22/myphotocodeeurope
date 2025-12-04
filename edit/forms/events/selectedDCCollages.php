<?php
clearstatcache();
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

// Load CSS + JS properly
$content = "<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>";
$content .= "<script src='sections/events/resources/js/printCollage.js'></script>";

$title = "Select DC collage";
$r = rand(0, 1000);

// Initialize collage with safe default
$collage = "";

$CLD_CON->OpenRs("SELECT collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $collage = $CLD_CON->GetArrayField("collage") ?? "";
}

// Process collage string to array
$photo = str_replace("/", "", $collage);
$photo = explode(";", $photo);
$collageArr = explode(";", $collage);
$llargada = sizeof($photo);

$content .= "<div id='mainDcFrames'>";
$content .= "<div id='contentDcFrames' style='margin-top:10px'>";

$content .= "<div id='arrow_left' onclick='anterior($llargada)'></div>";

// Determine start index - if first element is empty (leading semicolon), start at 1
$startPos = (empty($collageArr[0])) ? 1 : 0;

for ($pos = $startPos; $pos < $llargada; $pos++) {
    // Get collage folder name from saved value (e.g., "Halloween/1" -> folder is "Halloween")
    $collageParts = explode("/", $collageArr[$pos]);
    $collage_folder = $collageParts[0] ?? "";
    $collage_num = $collageParts[1] ?? "1";
    
    // Build image URLs from library/collages
    $url1 = "library/collages/{$collage_folder}/{$collage_num}_1.png";
    $url2 = "library/collages/{$collage_folder}/{$collage_num}_2.png";
    $url3 = "library/collages/{$collage_folder}/{$collage_num}_3.png";
    $url4 = "library/collages/{$collage_folder}/{$collage_num}_4.png";
    
    if ($pos == $startPos) {
        $content .= <<<HTML
        <div class='show' id='$pos'>
            <div>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url1?$r'>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url2?$r'>
            </div>
            <div>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url3?$r'>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url4?$r'>
            </div>
            <div class='checkboxframe'>
                <div id="{$photo[$pos]}" class='checked' onclick="check({$ID}, '{$collageArr[$pos]}', '{$photo[$pos]}', 1)" name='collage' checked></div>
            </div>
        </div>
HTML;
    } else {
        $content .= <<<HTML
        <div class='hidden' id='$pos'>
            <div>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url1?$r'>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url2?$r'>
            </div>
            <div>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url3?$r'>
                <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcCollage" src='$url4?$r'>
            </div>
            <div class='checkboxframe'>
                <div id="{$photo[$pos]}" class='checked' onclick="check({$ID}, '{$collageArr[$pos]}', '{$photo[$pos]}', 1)" name='collage' checked></div>
            </div>
        </div>
HTML;
    }
}

if ($llargada <= 2) {
    $content .= "<div id='arrow_right hidden' onclick='seguent($llargada)'></div>";
} else {
    $content .= "<div id='arrow_right' onclick='seguent($llargada)'></div>";
}

$content .= "</div>";
$content .= "</div>";

$cl = implode(";", $collageArr);

$buttons = "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save_collage($ID); hidePopupv2();'>";
$buttons .= "<input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='cancel_collage({$ID}, \"{$cl}\"); hidePopupv2();'>";

$content .= "<input type='hidden' id='dCollage'>";

$array_result = array('title' => $title, 'content' => $content, 'buttons' => $buttons);

echo json_encode($array_result);
clearstatcache();