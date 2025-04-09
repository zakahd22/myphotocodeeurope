<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$txt = $_POST['t'];
 if (!file_exists('../../../printPhoto/e' . $ID . '/PhotoIdUpload/')) {
            mkdir('../../../printPhoto/e' . $ID . '/', 0777);
            mkdir('../../../printPhoto/e' . $ID . '/PhotoIdUpload/', 0777);
 }
     
        
        
$file = fopen("../../../printPhoto/e$ID/PhotoIdUpload/text.txt", "w");
fwrite($file, $txt);
fclose($file);
echo "OK";
?>
