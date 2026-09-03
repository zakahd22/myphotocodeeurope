<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON->OpenRs("SELECT collage FROM event_frame WHERE id_event='d'");
while ($CLD_CON->FetchArray()) {
    $collage = $CLD_CON->GetArrayField("collage");
}
$title = substr($collage, 0, -2);
    
$num_borra = substr($collage, -1); 

$i = $num_borra +1;

$total_imagenes = count(glob("../../../library/collages/".$title."/{*_1.png}",GLOB_BRACE));
$total_imagenes = $total_imagenes - $num_borra;

/*elimina collages marcats*/
unlink("./../../../library/collages/".$title."/".$num_borra."_1.png");
unlink("./../../../library/collages/".$title."/".$num_borra."_2.png");
unlink("./../../../library/collages/".$title."/".$num_borra."_3.png");
unlink("./../../../library/collages/".$title."/".$num_borra."_4.png");

/*renombra els collages amb el num anterior desde el que s'ha eliminat*/
while($total_imagenes != 0){
    rename("./../../../library/collages/".$title."/".$i."_1.png" , "./../../../library/collages/".$title."/".$num_borra."_1.png");
    rename("./../../../library/collages/".$title."/".$i."_2.png" , "./../../../library/collages/".$title."/".$num_borra."_2.png");
    rename("./../../../library/collages/".$title."/".$i."_3.png" , "./../../../library/collages/".$title."/".$num_borra."_3.png");
    rename("./../../../library/collages/".$title."/".$i."_4.png" , "./../../../library/collages/".$title."/".$num_borra."_4.png");

    $total_imagenes = $total_imagenes -1;
    $i = $i +1;
    $num_borra = $num_borra +1;

}
$CLD_CON->OpenRs("SELECT num_packs FROM collages WHERE title = $title");
while ($CLD_CON->FetchArray()) {
    $num_packs = $CLD_CON->GetArrayField("num_packs");
}
$num_packs = $num_packs -1;
$CLD_CON->OpenRs("UPDATE collages SET num_packs=num_packs-1 WHERE title='$title'");        


