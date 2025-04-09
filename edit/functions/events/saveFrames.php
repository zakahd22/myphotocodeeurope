<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();

$ID = $_POST['id'];
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
$frame = explode(";",$frame);
$llargada = sizeof($frame);
if($llargada == 0){
    $num_c = 1;
}
$e = 0;
for ($pos = 1; $pos < $llargada; $pos++){
    $custom = substr($frame[$pos], 0, 6);
    if($custom == 'custom'){
        $num_c = substr($frame[$pos], -1) +1;
        $e = 1;
    }
}
if($e == 0){
   $num_c = 1; 
}
//$custom = substr($frame[$pos], -6);
utils::log($num_c, "logasd");
$total_imagenes = count(glob("../../../printPhoto/e$ID/PhotoIdUpload/Frames/{*.png}",GLOB_BRACE));

if($total_imagenes ==0){
    $num = 1;
}else{
    $num = ($total_imagenes/4)+1;
   
}

if($id_event == $ID){
    
    $frame[] = 'custom/'.$num_c;
    $cadena = implode(";", $frame);
    $CLD_CON->OpenRs("UPDATE event_frame SET frame='$cadena' WHERE id_event=$ID"); 
}else {
    $frame[] = 'custom/'.$num_c;
    $cadena = implode(";", $frame);
    $CLD_CON->OpenRs("INSERT INTO event_frame(id_event, frame) VALUES ('$ID', '$cadena')"); 
}

if (copy("./../../../printPhoto/tmp/fr".$ID."1.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$num."a.png")){          
       $x1 = true;
}
if (copy("./../../../printPhoto/tmp/fr".$ID."2.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$num."b.png")){          
       $x2 = true;
}
if (copy("./../../../printPhoto/tmp/fr".$ID."3.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$num."c.png")){          
       $x3 = true;
}
if (copy("./../../../printPhoto/tmp/fr".$ID."4.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$num."d.png")){          
       $x4 = true;
}

if($x1 && $x2 && $x3 && $x4){
    unlink("./../../../printPhoto/tmp/fr".$ID."1.png");
    unlink("./../../../printPhoto/tmp/fr".$ID."2.png");
    unlink("./../../../printPhoto/tmp/fr".$ID."3.png");
    unlink("./../../../printPhoto/tmp/fr".$ID."4.png");
   
}else{
    echo "ERROR";
}

//$total_imagenes1 = count(glob("../../../printPhoto/e$ID/PhotoIdUpload/Frames/{*.png}",GLOB_BRACE))/4;

//if($total_imagenes1 > 0){
$html ="<div id='butons'>
            <input id='edita' type='button' class='editButton' onclick='edit(72 , $ID);'>
            <input id='borra' type='button' class='miniTrash' onclick='deleteSelectFrames({$ID});'>
        </div>
     <div class='select_text'>&nbsp &nbsp you have selected ".$llargada." new frames sets</div>";
//}



echo $html;

?>
