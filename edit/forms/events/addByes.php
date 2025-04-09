<?php
header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
header ("Pragma: no-cache"); 
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $ID");
$CLD_CON->FetchArray();
$boothType = $CLD_CON->GetArrayField('boothtype_char');
$creationDate = $CLD_CON->GetArrayField("creation_date");
$eventID = $CLD_CON->GetArrayField("event_id"); 
$CLD_CON->OpenRs("SELECT b.* FROM booth_types b WHERE b.char='$boothType'");
$CLD_CON->FetchArray();
$bye_width = $CLD_CON->GetArrayField('welcome_w');
$bye_height = $CLD_CON->GetArrayField('welcome_h');
$screens = $CLD_CON->GetArrayField('screens');

$title = "Byes";

$content = "";
$buttons = "";
$content .= "<div class='popup-text'>";
$content .= "Upload $screens image/s. File type: JPG , File size:". $bye_width." x ".$bye_height." (pixels)";
$content .= "</div>";
$x = 1;

while ($x < $screens + 1) {
    $content .= "<div class='framesADD'>";
    $content .= "<div class='upload-dos'>";
    $content .= "<form id='frameForm$x' action='edit/functions/events/uploadByes.php' enctype='multipart/form-data'>";
    $content .= "<input type='file' class='popup-input-large' name='fileByes' id='fileByes$x'>";
    $content .= "<input type='hidden' value='$ID' name='id'>";
    $content .= "<input type='hidden' value='$x' name='bye'>";
    $content .= "<input type='hidden' name='hBye' value='$bye_height'>";
    $content .= "<input type='hidden' name='wBye' value='$bye_width'>";
    $content .= "</form>";
    $content .= "</div>";    
    $content .= "<div class='framePreview' id='fP$x'>";
    $content .= "</div>";    
    $content .= "</div>";
    $x++;
}
$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveByes($ID , $screens , $creationDate);'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";


$x=1;
$content .= '
<script>
$(document).ready(function(){
';

while ($x < $screens + 1) {
    $content .= "
        $('#fileByes{$x}').on('change', function() {
         uploadByes({$x});
        });
    ";
    $x++;
}

$content .= <<<HTML
});

function uploadByes(fr) {
        if ($("#fileFrame" + fr).val() === "") {
        } else {
            $("#frameForm" + fr).ajaxForm({
                success: function(e) {
                    if (e === "ERROR") {
                        swal("Error", "", "error");
                    } else {
                        $("#fP" + fr).html("<img src='printPhoto/tmp/" + e + "' style='width:100%; margin-left:10px; margin-top:1px;'>");
                        $("#fP" + fr).show(500);
                    }
                },
                error: function(e) {

                }
            });
            $("#frameForm" + fr).submit();
        }
 }
 
 function saveByes(id , screens , d) {   
            var bbb = $("#SELECTEDBOOTH").val();
            var ajaxData = {id: id , screens : screens , cDate : d};
            $.ajax({
                url: 'edit/functions/events/saveByes.php',
                type: 'POST',
                success: function(data) {
                    if (data === "OK") {
                        hidePopupv2();
//                        closePopup();
                        profile("events", "photobooths", $eventID);
                        var f = d + "" + id;
                        var a = '$boothType';
                        setTimeout(function(){
                            $("#setString" + id).val("2");
                            canviaApartat2(id , a , f , 2, bbb );
                        } , 1500);
                        
                    }else{
                       swal("Error","Please select the " + screens + " GoodBye screens.", "error");
                    }
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
 } 



</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);
