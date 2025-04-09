<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$imageName = $_POST['code'];

$trozos = explode(".", $imageName);
$extension = $trozos[1];
$code = $trozos[0];
$CLD_CON->OpenRs("SELECT e.start_date , e.id FROM photos p LEFT JOIN events e ON p.event_id = e.id WHERE p.code='$code'");
if($CLD_CON->FetchArray()){
    $eventID = $CLD_CON->GetArrayField("id");
    $date = $CLD_CON->GetArrayField("start_date");
    $imgPath =  "events/" . $date . $eventID . "/" . $code . ".jpg";
    $imgPath3D = "events/" . $date . $eventID . "/" . $code . "-T3D.gif";
}
/*TREURE UNA CARPETA*/
list($width, $height) = getimagesize('../../../' . $imgPath);
//$imgPath = $URL_LOGIN . $imgPath;
if ($height < 710) {
    echo "<img src='" . $imgPath . "' class='photoX'  >";
} else {
    echo "<img src='" . $imgPath . "' class='photoY' onclick='photoClick(event);'>";
    if(file_exists('../../../'.$imgPath3D)){
        echo "<img src='" .$imgPath3D. "' class='photoY' onclick='photoClick(event);'>";
    }else{
        if(        file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T1.jpg")
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T2.jpg")
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T3.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T4.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T5.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T6.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T7.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T8.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T9.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T10.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T11.jpg") 
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T12.jpg")
                && file_exists('../../../events/'. $date . $eventID . "/" . $code . "-3D/".$code."-T13.jpg")){
            include "../../../sections/photos/functions/gifEncoder.php";
            $folder3D = $_SERVER['DOCUMENT_ROOT'] . '/events/'. $date . $eventID . "/" . $code . "-3D/";
            $folder2D = $_SERVER['DOCUMENT_ROOT'] . '/events/'. $date . $eventID . "/";
            include "../../../sections/photos/functions/generate_3d.php";
            echo "<img src='" .$imgPath3D. "' class='photoY' onclick='photoClick();'>";
        }
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
    $(document).keyup(function(e) {
        if (e.keyCode == 27) { 
            closePhoto();
        }   // esc
    });
</script>    