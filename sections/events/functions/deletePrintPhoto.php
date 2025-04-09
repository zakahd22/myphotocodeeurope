<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$section = $_POST['s'];
$ID = $_POST['event'];
$i = $_POST['id2'];


switch ($section) {
    case 1://Logo
        $x = false;

        $f1 = $URL . "/printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
        $c1 = "printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
        $file_headers = @get_headers($f1);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $x = true;
        } else {
            if (unlink(G_PATH.$c1)) {
                $x = true;
            }
        }
        $CLD_CON->OpenRs("SELECT creation_date , id FROM usbs WHERE event_id=$ID");
        while ($CLD_CON->FetchArray()) {
            $creation_date = $CLD_CON->GetArrayField("creation_date");
            $ids = $CLD_CON->GetArrayField("id");
            $f2 = $URL_LOGIN . "usbs/" . $creation_date . $ids . "/PhotoIdUpload/Logo.jpg";
            $c2 = "usbs/" . $creation_date . $ids . "/PhotoIdUpload/Logo.jpg";
            $file_headers2 = @get_headers($f2);
            if ($file_headers2[0] == 'HTTP/1.1 404 Not Found') {
                
            } else {
                /* TREURE UNA CARPETA */
                //utils::log(G_PATH.$c2, 'deletePrintPhoto');
                unlink(G_PATH.$c2);
              
            }
        }
        if ($x) {
            echo "Logo is deleted";
        } else {
            echo "ERROR:I can't delete the logo , please try again";
        }
        break;
    case 2:
        unlink("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."a.png");
        unlink("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."b.png");
        unlink("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."c.png");
        unlink("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."d.png");
        $c= $i+1;
        while($i<25){
            if(file_exists("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$c."a.png")){
                rename("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$c."a.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."a.png");
                rename("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$c."b.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."b.png");
                rename("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$c."c.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."c.png");
                rename("../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$c."d.png", "../../../printPhoto/e$ID/PhotoIdUpload/Frames/".$i."d.png");
            }
            $c++;
            $i++;
            
        }

        break;
}

?>
