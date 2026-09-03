
<?php

$content .= '<link rel="stylesheet" type="text/css" href="sections/templates/resources/templates.css">';
$content .= '<script type="text/javascript" src="sections/templates/functions/functions.js"></script>';

$title .= "New Default Collages";

$content .= "";
$content .= "";

$content .= "<div class='Expression'>File type: PNG, File size: 1280x960</div>";
$content .= "<div class='Britta'>Resolucion: 300dpi.</div>";

$content .= "<div class='popup-text'>";
$content .= "</div>";

$content .= "<div style='margin-top:10px;'>";

$content .= "<select id='id_title' class='select'>";
$content .= "<option value=0>None</option>";
$CLD_CON->OpenRs("SELECT id , title FROM collages");
while ($CLD_CON->FetchArray()) {
    $collage_id = $CLD_CON->GetArrayField("id");
    $collage_title = $CLD_CON->GetArrayField("title");
    $collage_title = str_replace("_", " ", $collage_title);
    $content .= "<option value='$collage_id'>$collage_title</option>";  
}
$content .= "</select>";

//FRAME1
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm1' action='edit/functions/events/uploadTemplate.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage1' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='1' name='collage'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP1'>";
$content .= "</div>";

$content .= "</div>";

//FRAME2
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm2' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage2' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='2' name='collage'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP2'>";
$content .= "</div>";

$content .= "</div>";

//FRAME3
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm3' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage3' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='3' name='collage'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP3'>";
$content .= "</div>";

$content .= "</div>";

//FRAME4
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm4' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage4' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='4' name='collage'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP4'>";
$content .= "</div>";

$content .= "</div>";

$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' style='margin-left: 25px;' value='Save' onclick='saveTemplates();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);