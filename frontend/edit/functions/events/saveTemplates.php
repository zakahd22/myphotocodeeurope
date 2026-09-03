<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$id_title = $_POST['id_title'];

$CLD_CON->OpenRs("SELECT title FROM collages WHERE id=$id_title");
while ($CLD_CON->FetchArray()) {
    $title = $CLD_CON->GetArrayField("title");
}

$title = str_replace(" ", "_", $title);
$total_imagenes = count(glob("../../../library/collages/".$title."/{*_1.png}",GLOB_BRACE));

if($total_imagenes ==0){
    $num = 1;
}else{
    $num = $total_imagenes+1;
}

if (copy("./../../../printPhoto/tmp/cl1.png" , "./../../../library/collages/".$title."/".$num."_1.png")){          
        $x1 = true;
}
if (copy("./../../../printPhoto/tmp/cl02.png" , "./../../../library/collages/".$title."/".$num."_2.png")){          
        $x1 = true;
}
if (copy("./../../../printPhoto/tmp/cl03.png" , "./../../../library/collages/".$title."/".$num."_3.png")){          
        $x1 = true;
}
if (copy("./../../../printPhoto/tmp/cl04.png" , "./../../../library/collages/".$title."/".$num."_4.png")){          
        $x1 = true;
}
if($x1 && $x2 && $x3 && $x3){
    unlink("./../../../printPhoto/tmp/cl1.png");
    unlink("./../../../printPhoto/tmp/cl02.png");
    unlink("./../../../printPhoto/tmp/cl03.png");
    unlink("./../../../printPhoto/tmp/cl04.png");
    echo "OK";
}else{
    utils::log("ERROR", "logasd");
}

$CLD_CON->OpenRs("UPDATE collages SET num_packs='$num' WHERE title='$title'");        