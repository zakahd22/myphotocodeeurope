<meta http-equiv="expires" content="0">
 
<meta http-equiv="Cache-Control" content="no-cache">
 
<meta http-equiv="Pragma" CONTENT="no-cache">
<?php
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$ID = $_POST['id'];
$c1 = "printPhoto/e$ID/";
$html = <<<HTML
<link rel='stylesheet' href='sections/events/resources/css/printPhotos.css'>
<script src='sections/events/resources/js/printPhotos.js'/>
<div class='inContent'>
    <div class='boxLeft'>
        <div class='box'>
HTML;

$rnd = rand(0, 800000) / rand(1, 5000);
 $CLD_CON->OpenRs("SELECT creation_date , id FROM usbs WHERE event_id=$ID");
    while ($CLD_CON->FetchArray()) {
        $creation_date = $CLD_CON->GetArrayField("creation_date");
        $i = $CLD_CON->GetArrayField("id");
        $c2 = "usbs/" . $creation_date . $i . "/";
    }

$folderName1 = $URL . "/printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
/* TREURE CARPETA */
if (!file_exists(G_PATH . "/printPhoto/e$ID/PhotoIdUpload/Logo.jpg")) {
    $CLD_CON->OpenRs("SELECT creation_date , id FROM usbs WHERE event_id=$ID");
    while ($CLD_CON->FetchArray()) {
        $creation_date = $CLD_CON->GetArrayField("creation_date");
        $i = $CLD_CON->GetArrayField("id");
        $folderName2 = $URL_LOGIN . "usbs/" . $creation_date . $i . "/PhotoIdUpload/Logo.jpg";
        $c2 = "usbs/" . $creation_date . $i . "/";
        $file_headers2 = @get_headers($folderName2);
        if (!file_exists(G_PATH . "usbs/$creation_date$i/PhotoIdUpload/Logo.jpg")) {
            $exists = false;
        } else {
            $exists = true;
            /* TREURE CARPETA */
            if (!file_exists(G_PATH . "/printPhoto/e$ID/PhotoIdUpload/")) {
                mkdir(G_PATH . "printPhoto/e$ID/PhotoIdUpload/", 0777, true);
            }
            /* TREURE CARPETA */
            
            if (copy(G_PATH . "usbs/" . $creation_date . $i . "/PhotoIdUpload/Logo.jpg", G_PATH . "printPhoto/e$ID/PhotoIdUpload/Logo.jpg")) {
                $logo = "printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
            } else {
                $logo = "usbs/" . $creation_date . $i . "/PhotoIdUpload/Logo.jpg";
            }
        }
    }
} else {
    $exists = true;
    $logo = "printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
}
            $html .= "<h1>Logo<input type='button' class='editButton' onClick='edit(21 , $ID);'>";
if ($exists) {
            $html .= "<input type='button' class='miniTrash' onclick='deletePrintPhoto(1 , $ID , 0 , \"logo\");'>";
}
            $html .= "</h1>";
if ($exists) {
            $html .= "<img src='$logo' class='logo_usb'>";
} else {
            $html .= "<p> No Logo yet </p>";
}

$html .= <<<HTML
            <img src='images/web/custom/logo.png?version=$rnd;' class='imgInfoLogo'>
        </div>
        <div class='box_abaix'>
            <h1 class="titleBox_abaix">Frames </h1>
            <div class='custom'>
                <p>
                    <div class='UploadCustom' onclick='edit(23 , $ID);'></div>

                </p>    
                <div class='CustomFrames' onclick='edit(23 , $ID);'></div>
            </div>
            <div class='custom'>
                <p>
                    <div class='SelectDCFrame' onclick='edit(24 , $ID);'></div>
                </p>
                <div class='DCFrames' onclick='edit(24 , $ID);'></div>

            </div>
        </div>
    </div>

    <div class='boxRight'>
    <div class='box'>

    <h1>Text <input type='button' class='editButton' onClick='edit(22 , $ID);'></h1>
HTML;

$folderName1 = G_PATH . $c1 . "PhotoIdUpload/text.txt";
$folderName2 = G_PATH . $c2 . "PhotoIdUpload/text.txt";

if (file_exists($folderName1)) {
    $text = file_get_contents($folderName1);
} else {
    if (file_exists($folderName2)) {
        copy($folderName2, $folderName1);
        $text = file_get_contents($folderName2);
    } else {
        $text = "No text";
    }
}


$html .= <<<HTML
    <p>Your selected text :</p>
    <p style='width: 37%;'>$text</p>
    <img src='images/web/custom/text.png' class='imgInfoLogo' id='".$c1."'>

    </div>
    <div class='box_abaix'>
        <h1 class="titleBox_abaix">Collages </h1>
        <div class='custom'>
            <p>
                <div class='UploadCustom' onclick='edit(75 , $ID);'></div>
            </p>
            <div class='CustomCollage' onclick='edit(75 , $ID);'></div>
        </div>
        <div class='custom'>
            <p>
                <div class='SelecdDCCollage' onclick='edit(73 , $ID);'></div>
            </p>
            <div class='DCCollage'onclick='edit(73 , $ID);'></div>
        </div>
    </div>
</div>
</div>
</div>
<div id='selectedFrames' class='selectedFrames'>
HTML;


$CLD_CON->OpenRs("SELECT frame FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {    
    $frame = $CLD_CON->GetArrayField("frame"); 
}
$frame = explode(";",$frame);
$total_fr = sizeof($frame) - 1;

$CLD_CON->OpenRs("SELECT collage FROM event_frame WHERE id_event = $ID");
while ($CLD_CON->FetchArray()) {
    $collage = $CLD_CON->GetArrayField("collage");
}
$collage = explode(";", $collage);
$total_cl = sizeof($collage);
$total_imagenes = count(glob('../../../printPhoto/e'.$ID.'/PhotoIdUpload/Collage/{*.png}',GLOB_BRACE))/4;

$html .= "<div id='butons'>
            <input id='edita' type='button' class='editButton' onclick='edit(72 , $ID);'>
            <input id='borra' type='button' class='miniTrash' onclick='deleteSelectFrames({$ID});'>
        </div>
     <div class='select_text'>&nbsp &nbsp you have selected " . $total_fr . " new frames sets</div>";

$html .= '</div>';
$html .= "<div id='selectedCollage' class='selectedCollage'>";

$html .= "<div id='butons'>
            <input id='edita' type='button' class='editButton' onclick='edit(74 , $ID)'>
            <input id='borra' type='button' class='miniTrash' onclick='deleteSelectCollages({$ID})'>
        </div>
        <div class='select_text'>&nbsp &nbsp you have selected " . $total_imagenes . " new collages sets</div>";

$html .= '</div>';

function listarArchivos($path, $d, $e) {
    // Abrimos la carpeta que nos pasan como parámetro
    $x = 1;
    /* TREURE UNA CARPETA */
    if ($d == 1) {
        $U = "";
    } else {
        $U = "../";
    }

    $dir = opendir($U . "../../../" . $path);
    // Leo todos los ficheros de la carpeta
    while ($elemento = readdir($dir)) {
        // Tratamos los elementos . y .. que tienen todas las carpetas
        if ($elemento != "." && $elemento != "..") {
            // Si es una carpeta
            if (!is_dir($path . $elemento)) {
                $rnd2 = rand(0, 800000) / rand(1, 5000);
                echo "<img src='" . $U . $path . $elemento . "?version=$rnd2;' class='eventFrames'>";
                if ($x % 4 == 0) {
                    $c = $x / 4;
                    $html .= "<p style='color:red;text-align:center;cursor:pointer;border-bottom:1px solid gray;padding-bottom:10px;'>";
                    $html .= "<input type='button' class='miniTrash' style='right:-47%;' onclick='deletePrintPhoto(2 , $e , $c , \"Frames #$c\");'>";
                    $html .= "</p>";
                }
                $x++;
            }
        }
    }
}

echo $html;

function copiarArchivos($fol1, $fol2) {
    if (!file_exists($fol2)) {
        mkdir($fol2, 0777, true);
    }
    $dir = opendir($fol1);
    while ($elemento = readdir($dir)) {
        if ($elemento != "." && $elemento != "..") {
            if (!is_dir($fol1 . $elemento)) {

                move_uploaded_file($fol1 . $elemento, $fol2 . $elemento);
            }
        }
    }
}
?>
