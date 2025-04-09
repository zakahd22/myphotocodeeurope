<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$content = "<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>";
$content .= "<script src='sections/events/resources/js/printCollage.js'/>";
$content .= "<script> nou_num = 0;arrayShow = ['1', '2'];</script>";
$json = json_decode($_POST["data"], TRUE);
$id = $json[0];


$title = "Select DC collage";

$CLD_CON->OpenRs("SELECT collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $collage = $CLD_CON->GetArrayField("collage");      
}

/*cadena to array*/
$photo = str_replace("/", "", $collage);

$llargada = sizeof($photo);

$content .= "<div id='mainDcFrames'>";
$content .= "<div id='contentDcFrames' style='margin-top:10px'>";

$pos = 0;

$content .= <<<HTML
<div class='show' id='$pos' >
    <div>
        <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='printPhoto/e{$ID}/PhotoIdUpload/Collage/lay1.png'>
        <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='printPhoto/e{$ID}/PhotoIdUpload/Collage/lay2.png'>
    </div>
    <div>
        <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='printPhoto/e{$ID}/PhotoIdUpload/Collage/lay3.png'>
        <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='printPhoto/e{$ID}/PhotoIdUpload/Collage/lay4.png'>
    </div>
    <div class='checkboxframe'>
    <div id="$photo" class='checked ' onclick="check({$ID}, '{$collage}', '{$photo}', 1)" name='frame' checked></div>
    </div>
</div>   
HTML;
    
$content .= "</div>";
$content .= "</div>";
$content .= "<input type='hidden' id='dFrame'>";

$buttons = "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save_collage($ID); hidePopupv2();'>";
$buttons .= "<input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='cancel_collage({$ID}, \"{$collage}\"); hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);