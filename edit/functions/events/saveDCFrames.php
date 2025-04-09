<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();

$json = json_decode($_POST["data"], TRUE);

$ID = $json[0];
$ant_fr = $json[1];
$ant_fr = str_replace("/", "", $ant_fr);
utils::log($ant_fr, "logasd");
$ant_fr = explode(";",$ant_fr);

if (!file_exists("../../../printPhoto/e$ID/PhotoIdUpload/")) {
    mkdir("../../../printPhoto/e$ID/", 0777, true);
    mkdir("../../../printPhoto/e$ID/PhotoIdUpload/", 0777, true);
}
if (!file_exists(  "../../../printPhoto/e$ID/PhotoIdUpload/Frames/")) {
    mkdir("../../../printPhoto/e$ID/PhotoIdUpload/Frames/", 0777, true);
}

$x1 = false;
$x2 = false;
$x3 = false;
$x4 = false;


$CLD_CON->OpenRs("SELECT id_event, frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $frame = $CLD_CON->GetArrayField("frame"); 
}
$photo = str_replace("/", "", $frame);
$photo = explode(";",$photo);
$frame = explode(";",$frame);
$total_fr = sizeof($frame);


$total_imagenes = count(glob('./../../../printPhoto/tmp/fr*',GLOB_BRACE))/4;

$num = 0;
$pos = 1;
foreach ($photo as $custom) {
    $custom = substr($custom, 0, 6);
    if ($custom == "custom") {
        if($photo[$num] == $ant_fr[$num]){
            utils::log("si","logasd");
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num . "a.png", "./../../../printPhoto/tmp/fr" . $pos . "1.png")) {
            $x1 = true;
            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num . "b.png", "./../../../printPhoto/tmp/fr" . $pos . "2.png")) {
                $x2 = true;
            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num . "c.png", "./../../../printPhoto/tmp/fr" . $pos . "3.png")) {
                $x3 = true;
            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num . "d.png", "./../../../printPhoto/tmp/fr" . $pos . "4.png")) {
                $x4 = true;
            }
            $pos++;
        }else{
            utils::log("no","logasd");
            $num ++;
//            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num. "a.png", "./../../../printPhoto/tmp/fr" . $pos . "1.png")) {
                $x1 = true;
//                chmod("./../../../printPhoto/tmp/fr" . $pos . "1.png", 0774);
            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num. "b.png", "./../../../printPhoto/tmp/fr" . $pos . "2.png")) {
                $x2 = true;
//                chmod("./../../../printPhoto/tmp/fr" . $pos . "2.png", 0774);
            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num. "c.png", "./../../../printPhoto/tmp/fr" . $pos . "3.png")) {
                $x3 = true;
//                chmod("./../../../printPhoto/tmp/fr" . $pos . "3.png", 0774);
            }
            if (copy("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $num. "d.png", "./../../../printPhoto/tmp/fr" . $pos . "4.png")) {
                $x4 = true;
//                chmod("./../../../printPhoto/tmp/fr" . $pos . "4.png", 0774);
            }
        $pos++;
        utils::log($pos, "logasd");
        }
    }
    $num++;

    $total_imagenes++;
}

$files = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Frames/*"); // get all file names
foreach ($files as $fr) { // iterate files
    unlink($fr);
}
$num = 1;
$n = 0;
foreach ($photo as $custom) {
    
//    utils::log($n, "logasd");
    $custom = substr($custom, 0, 6);
    
    if ($custom != "custom") {

//                    utils::log($n, "logasd");

//        for ($i = 0; $i < $total_fr; $i++) {
            if (copy("../../../library/frames/" . $frame[$n] . "_1.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "a.png")) {
                $x1 = true;
                chmod("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "a.png", 0774);
            }
            if (copy("../../../library/frames/" . $frame[$n] . "_2.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "b.png")) {
                $x2 = true;
                chmod("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "b.png", 0774);
            }
            if (copy("../../../library/frames/" . $frame[$n] . "_3.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "c.png")) {
                $x3 = true;
                chmod("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "c.png", 0774);
            }
            if (copy("../../../library/frames/" . $frame[$n] . "_4.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "d.png")) {
                $x4 = true;
                chmod("../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n . "d.png", 0774);
            }
            
//        }
//        $n++;
            
//            utils::log("======================", "logasd");
//            utils::log($n, "logasd");
//        $n++;

    } else {
        $total_frames = count(glob('../../../printPhoto/e' . $ID . '/PhotoIdUpload/Frames/{*.png}', GLOB_BRACE)) / 4;
//        utils::log($total_frames, "logasd");
        $total_frames++;
        
//        if($n == 1){
//            $n2 = $n;
//        }else{
            $n2 = $n;
//        }
        
//    foreach($photo as $custom){
//        $custom = substr($custom, 0, 6);
//        if($custom == "custom"){
        if (copy("./../../../printPhoto/tmp/fr" . $num . "1.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n2 . "a.png")) {
            $x1 = true;
        }
        if (copy("./../../../printPhoto/tmp/fr" . $num . "2.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n2 . "b.png")) {
            $x2 = true;
        }
        if (copy("./../../../printPhoto/tmp/fr" . $num . "3.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n2 . "c.png")) {
            $x3 = true;
        }
        if (copy("./../../../printPhoto/tmp/fr" . $num . "4.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/" . $n2 . "d.png")) {
            $x4 = true;
        }
        $num++;
//        $n = $n + 1;
        
    }
            $n++;
//            utils::log("======================", "logasd");

//    }
}

//$total_imagenes = count(glob('../../../printPhoto/e' . $ID . '/PhotoIdUpload/Frames/{*.png}', GLOB_BRACE)) / 4;
//if ($total_imagenes > 0) {
$total_fr = $total_fr -1;
    $html = "<div id='butons'>
            <input id='edita' type='button' class='editButton' onclick='edit(72 , $ID);'>
            <input id='borra' type='button' class='miniTrash' onclick='deleteSelectFrames({$ID});'>
        </div>
     <div class='select_text'>&nbsp &nbsp you have selected " . $total_fr. " new frames sets</div>";
//}

$total_imagenes = count(glob('./../../../printPhoto/tmp/fr*',GLOB_BRACE))/4;
utils::log($total_imagenes, "logasd");
for ($i = 1; $i <= $total_imagenes; $i++){
    unlink("./../../../printPhoto/tmp/fr" . $i . "1.png");
    unlink("./../../../printPhoto/tmp/fr" . $i . "2.png");
    unlink("./../../../printPhoto/tmp/fr" . $i . "3.png");
    unlink("./../../../printPhoto/tmp/fr" . $i . "4.png");
}


echo $html;
