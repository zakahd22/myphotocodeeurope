<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$html .= "<link rel='stylesheet' href='/../../sections/events/resources/css/printPhotos.css'>";
$seg = 0;
$baseController = new baseController();
$json = json_decode($_POST["dades"], TRUE);
$nom_frame = $json[1];
$ID = $json[0];

$CLD_CON->OpenRs("SELECT id , title, num_packs  FROM frames WHERE id=$nom_frame");
while ($CLD_CON->FetchArray()) {
    $frame_id = $CLD_CON->GetArrayField("id");
    $frame_title = $CLD_CON->GetArrayField("title");       
    $num_packs = $CLD_CON->GetArrayField("num_packs");       
}

$CLD_CON->OpenRs("SELECT frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $frame = $CLD_CON->GetArrayField("frame");      
}
/*cadena to array*/
$fr = explode(";",$frame);

$llargada = sizeof($fr);
$html .= "<div id='arrow_left' onclick='anterior($num_packs)'></div>";

$existeix = false;

for ($i = 1; $i <= $num_packs; $i++) {
    $valor = $frame_title."/".$i;
    $id_check = $frame_title.$i;
    
    
    for ($pos = 0; $pos <= $llargada; $pos++){
        if($valor == $fr[$pos]){
            $existeix = true;
        }
    }
    if($i <= 3){
        $html .= <<<HTML
        <div class='contentimgDcFrames show' id='$i' >
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_1.png'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_2.png'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_3.png'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_4.png'>    
            
HTML;
            if($existeix == true){
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='checked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='frame' checked>
                        </div>
                    </div>
HTML;
                $existeix = false;
            }
            else{
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='unchecked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='frame'>
                        </div>
                    </div>
HTML;
            }
        $html .= "</div>";
    }
    else{
        $html .= <<<HTML
        <div class='contentimgDcFrames hidden' id='$i'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_1.png'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_2.png'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_3.png'>
            <img style="margin-top:10px; border: solid #333333 2px;" class="imgDcFrame" src='library/frames/$frame_title/{$i}_4.png'>
            
HTML;
            
            if($existeix == true){
                
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='checked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='frame' checked>
                        </div>
                    </div>
HTML;
                $existeix = false;
            }
            else{
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='unchecked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='frame'>
                        </div>
                    </div>
HTML;
            }
            
        $html .= "</div>";
    }
}
if ($num_packs > 3){
    $html .= "<div id='arrow_right' onclick='seguent($num_packs)'></div>";
}

echo $html;