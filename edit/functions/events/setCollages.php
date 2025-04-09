<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$html .= "<link rel='stylesheet' href='/../../sections/events/resources/css/printPhotos.css'>";
$seg = 0;
$baseController = new baseController();
$json = json_decode($_POST["dades"], TRUE);
$nom_collage = $json[1];
$ID = $json[0];
if($json[2] != NULL){
   $ID = $json[2]; 
}




$CLD_CON->OpenRs("SELECT id , title, num_packs  FROM collages WHERE id=$nom_collage");
while ($CLD_CON->FetchArray()) {
    $collage_id = $CLD_CON->GetArrayField("id");
    $collage_title = $CLD_CON->GetArrayField("title");       
    $num_packs = $CLD_CON->GetArrayField("num_packs");       
}

$CLD_CON->OpenRs("SELECT collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $collage = $CLD_CON->GetArrayField("collage");      
}


/*cadena to array*/

$cl = explode(";",$collage);

$llargada = sizeof($cl);
$html .= "<div id='arrow_left' onclick='anterior($num_packs)'></div>";
//$pos = 0;

$existeix = false;



for ($i = 1; $i <= $num_packs; $i++) {
    $valor = $collage_title."/".$i;
    $id_check = $collage_title.$i;
    
    
    for ($pos = 0; $pos <= $llargada; $pos++){
//    while($pos < $llargada) {
        if($valor == $cl[$pos]){
            $existeix = true;
        }
//        $pos = $pos + 1;
    }
//    $id_check = 1;
    if($i == 1){
        $html .= <<<HTML
        <div class='show' id='$i' >
            <div>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_1.png'>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_2.png'>
            </div>
            <div>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_3.png'>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_4.png'>
            </div>
HTML;
            if($existeix == true){
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='checked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='collage' checked>
                        </div>
                    </div>
HTML;
                $existeix = false;
//                                        <input type='checkbox' class='checkboxframe' event='$ID' id='$valor' name='collage' value='$valor' checked>

//              $pos = $pos + 1;
            }
            else{
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='unchecked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='collage'>
                        </div>
                    </div>
HTML;
            }
        $html .= "</div>";
    }
    else{
        $html .= <<<HTML
        <div class='hidden' id='$i'>
            <div>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_1.png'>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_2.png'>
            </div>
            <div>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_3.png'>
                <img class="imgDcCollage" src='library/collages/$collage_title/{$i}_4.png'>
            </div>
HTML;
            
            if($existeix == true){
                
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='checked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='collage' checked>
                        </div>
                    </div>
HTML;
                $existeix = false;
//<input type='checkbox' class='checkboxframe' event='$ID' id='$valor' name='collage' value='$valor' checked>

//              $pos = $pos + 1;
            }
            else{
                $html .= <<<HTML
                    <div class='checkboxframe'>
                        <div id="$id_check" class='unchecked ' onclick="check({$ID}, '{$valor}', '{$id_check}')" name='collage'>
                        </div>
                    </div>
HTML;
            }
            
        $html .= "</div>";
    }
}
if ($num_packs > 1){
    $html .= "<div id='arrow_right' onclick='seguent($num_packs)'></div>";
}
//$html .= "<input id='save' type='button' class='popup-confirm' value='Save' onclick='save()'>";
//$html .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";



echo $html;