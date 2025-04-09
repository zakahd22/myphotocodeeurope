<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
    if (!file_exists("../../../printPhoto/e$ID/PhotoIdUpload/")) {
            mkdir("../../../printPhoto/e$ID/", 0777, true);
            mkdir("../../../printPhoto/e$ID/PhotoIdUpload/", 0777, true);
        }
        if (!file_exists(  "../../../printPhoto/e$ID/PhotoIdUpload/Collage/")) {
            mkdir("../../../printPhoto/e$ID/PhotoIdUpload/Collage/", 0777, true);
        }
$x1 = false;
$x2 = false;
$x3 = false;
$x4 = false;

$CLD_CON->OpenRs("SELECT id_event, collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $collage = $CLD_CON->GetArrayField("collage"); 
}
$collage = explode(";",$collage);
$total_cl = sizeof($collage);
//$frame = explode(";",$frame);

if($id_event == $ID){
    $CLD_CON->OpenRs("UPDATE event_frame SET collage='custom/1' WHERE id_event=$ID"); 
}else {
    $CLD_CON->OpenRs("INSERT INTO event_frame(id_event, collage) VALUES ('$ID', 'custom/1')"); 
}

$collages = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Collage/*"); // get all file names

foreach($collages as $elimina){ // iterate files

    if(is_file($elimina)){
        unlink($elimina); // delete file
    }
}

$total_imagenes = count(glob("../../../printPhoto/e$ID/PhotoIdUpload/Collage/{*.png}",GLOB_BRACE));

if($total_imagenes ==0){
    $num = 1;
}else{
    $num = ($total_imagenes)+1;
}

if (copy("./../../../printPhoto/tmp/cl".$ID."1.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay1.png")){          
       $x1 = true;
}
if (copy("./../../../printPhoto/tmp/cl".$ID."2.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay2.png")){          
       $x2 = true;
}
if (copy("./../../../printPhoto/tmp/cl".$ID."3.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay3.png")){          
       $x3 = true;
}
if (copy("./../../../printPhoto/tmp/cl".$ID."4.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay4.png")){          
       $x4 = true;
}

//$total_imagenes1 = count(glob('../../../printPhoto/e'.$ID.'/PhotoIdUpload/Collage/{*.png}',GLOB_BRACE))/4;
//if($total_imagenes1 > 0){
    $html ="<div id='butons'>
            <input id='edita' type='button' class='editButton' onclick='edit(74 , $ID)'>
            <input id='borra' type='button' class='miniTrash' onclick='deleteSelectCollages({$ID})'>
        </div>
        <div class='select_text'>&nbsp &nbsp you have selected ".$total_cl." new collages sets</div>";
//}

echo $html;
?>
