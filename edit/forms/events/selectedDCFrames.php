
<?php
clearstatcache();
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
//utils::log("entra", "logasd");
$content .= "<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>";
$content .= "<script src='sections/events/resources/js/printPhotos.js'/>";
$json = json_decode($_POST["data"], TRUE);
$id = $json[0];
$r = rand (0, 1000);
$title = "Select frames";

$CLD_CON->OpenRs("SELECT frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $frame = $CLD_CON->GetArrayField("frame");      
}
//utils::log($frame, "logasd");
/*cadena to array*/
$photo = str_replace("/", "", $frame);
$photo = explode(";",$photo);
$frame = explode(";",$frame);
$llargada = sizeof($photo);
//utils::log($llargada, "logasd");
$content .= "<div id='mainDcFrames'>";
$content .= "<div id='contentDcFrames' style='margin-top:10px'>";

$content .= "<div id='arrow_left' onclick='anterior($llargada)'></div>";

for ($pos = 1; $pos < $llargada; $pos++){
    $custom = substr($frame[$pos], 0, 6);
    if($custom != 'custom'){
            $url1 = 'library/frames/'.$frame[$pos].'_1.png';
            $url2 = 'library/frames/'.$frame[$pos].'_2.png';
            $url3 = 'library/frames/'.$frame[$pos].'_3.png';
            $url4 = 'library/frames/'.$frame[$pos].'_4.png';
        }else{
            $url1 = 'printPhoto/e'.$ID.'/PhotoIdUpload/Frames/'.$pos.'a.png';
            $url2 = 'printPhoto/e'.$ID.'/PhotoIdUpload/Frames/'.$pos.'b.png';
            $url3 = 'printPhoto/e'.$ID.'/PhotoIdUpload/Frames/'.$pos.'c.png';
            $url4 = 'printPhoto/e'.$ID.'/PhotoIdUpload/Frames/'.$pos.'d.png';
        }
    if($pos <= 3){
        
        $content .= <<<HTML
        <div class='contentimgDcFrames show' id='$pos' >
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url1?$r' >
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url2?$r'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url3?$r'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url4?$r'>

            <div class='checkboxframe'>
            <div id="$photo[$pos]" class='checked ' onclick="check({$ID}, '{$frame[$pos]}', '{$photo[$pos]}')" name='frame' checked></div>
            </div>
        </div>   
HTML;
    }
    else{
        $content .= <<<HTML
        <div class='contentimgDcFrames hidden' id='$pos' >
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url1?$r'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url2?$r'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url3?$r'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='$url4?$r'>


            <div class='checkboxframe'>
            <div id="$photo[$pos]" class='checked ' onclick="check({$ID}, '{$frame[$pos]}', '{$photo[$pos]}')" name='frame' checked></div>
            </div>
        </div>   
HTML;
    }
}

if ($llargada <= 4){
    $content .= "<div id='arrow_right hidden' onclick='seguent($llargada)'></div>";
}else{
    $content .= "<div id='arrow_right' onclick='seguent($llargada)'></div>";
}

$content .= "</div>";
$content .= "</div>";
$fr = implode(";",$frame);

$buttons .= "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save({$ID}, \"{$fr}\"); hidePopupv2();'>";
$buttons .= "<input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='cancel({$ID}, \"{$fr}\"); hidePopupv2();'>";

$content .= "<input type='hidden' id='dFrame'>";


$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);
clearstatcache();