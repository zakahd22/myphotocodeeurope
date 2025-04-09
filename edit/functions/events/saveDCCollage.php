<?php 
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$baseController = new baseController();
$json = json_decode($_POST["data"], TRUE);
$ID = $json[0];
if($json[1] != NULL){
   $ID = $json[1]; 
}

if (!file_exists("../../../printPhoto/e$ID/PhotoIdUpload/")) {
    mkdir("../../../printPhoto/e$ID/", 0777, true);
    mkdir("../../../printPhoto/e$ID/PhotoIdUpload/", 0777, true);
}
if (!file_exists(  "../../../printPhoto/e$ID/PhotoIdUpload/Collage/")) {
    mkdir("../../../printPhoto/e$ID/PhotoIdUpload/Collage/", 0774, true);
}

$CLD_CON->OpenRs("SELECT id_event, collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $id_event = $CLD_CON->GetArrayField("id_event");      
    $collage = $CLD_CON->GetArrayField("collage"); 
}
$photo = str_replace("/", "", $collage);
$photo = explode(";",$photo);
$collage = explode(";",$collage);
$total_cl = sizeof($collage);

$collages = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Collage/*"); // get all file names

foreach($collages as $elimina){ // iterate files

    if(is_file($elimina)){
        unlink($elimina); // delete file
    }
}
//$collages = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Collage/*"); // get all file names
////utils::log($file, "logASD");
//foreach($collages as $elimina){ // iterate files
//    $ex = 0;
//    $customs = glob("../../../printPhoto/e{$ID}/PhotoIdUpload/Collage/custom*"); // get all file names
//    foreach($customs as $custom){
//        if ($custom == $elimina){
//            $ex = 1;
//        }
//    }
//    if($ex == 0){
//        unlink($elimina); // delete file
//    }
//}

for ($i = 0; $i < $total_cl; $i++){
    if (copy("../../../library/collages/" . $collage[$i] . "_1.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay1.png")){          
        chmod("../../../printPhoto/e$ID/PhotoIdUpload/Collage/1a.png", 0774); 
    }
    if (copy("../../../library/collages/" . $collage[$i] . "_2.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay2.png")){          
        chmod("../../../printPhoto/e$ID/PhotoIdUpload/Collage/1b.png", 0774); 
    }
    if (copy("../../../library/collages/" . $collage[$i] . "_3.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay3.png")){          
        chmod("../../../printPhoto/e$ID/PhotoIdUpload/Collage/1c.png", 0774); 
    }
    if (copy("../../../library/collages/" . $collage[$i] . "_4.png" , "../../../printPhoto/e$ID/PhotoIdUpload/Collage/lay4.png")){          
        chmod("../../../printPhoto/e$ID/PhotoIdUpload/Collage/1d.png", 0774); 
    }
}
$total_imagenes = count(glob('../../../printPhoto/e'.$ID.'/PhotoIdUpload/Collage/{*.png}',GLOB_BRACE))/4;
//if($total_imagenes > 0){
    $html ="<div id='butons'>
            <input id='edita' type='button' class='editButton' onclick='edit(74 , $ID)'>
            <input id='borra' type='button' class='miniTrash' onclick='deleteSelectCollages({$ID})'>
        </div>
        <div class='select_text'>&nbsp &nbsp you have selected ".$total_imagenes." new collages sets</div>";
//}



echo $html;