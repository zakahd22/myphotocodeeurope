<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $ID");
$CLD_CON->FetchArray();
$boothType = $CLD_CON->GetArrayField('boothtype_char');
$creationDate = $CLD_CON->GetArrayField("creation_date");
$eventID = $CLD_CON->GetArrayField("event_id"); 
$CLD_CON->OpenRs("SELECT b.* FROM booth_types b WHERE b.char='$boothType'");
$CLD_CON->FetchArray();
$shot_width = $CLD_CON->GetArrayField('custom_w');
$shot_height = $CLD_CON->GetArrayField('custom_h');
$screens = $CLD_CON->GetArrayField('screens');



$title = "Custom Shots";

$content = "";
$buttons = "";
$content .= "<div class='popup-text'>";
$content .= "You can upload all the Custom Shots you want one at a time.<br/>";
$content .= "File type: JPG , File size:". $shot_width." x ".$shot_height." (pixels)";
$content .= "</div>";
$x = 1;
while ($x < $screens + 1) {
    $content .= "<div class='framesADD'>";
    $content .= "<div class='upload-dos'>";
    $content .= "<form id='frameForm$x' action='edit/functions/events/uploadShot.php' enctype='multipart/form-data'>";
    $content .= "<input type='file' class='popup-input-large' name='fileShot' id='fileShot$x'>";
    $content .= "<input type='hidden' value='$ID' name='id'>";
    $content .= "<input type='hidden' value='$x' name='shot'>";
    $content .= "<input type='hidden' name='hWel' value='$shot_height'>";
    $content .= "<input type='hidden' name='wWel' value='$shot_width'>";
    $content .= "</form>";
    $content .= "</div>";  
    $content .= "<div class='framePreview' id='fP$x'>";
    $content .= "</div>";
    $content .= "</div>";
    $x++;
}

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveShots($ID , $screens , $creationDate)';>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";


$x=1;
$content .= '
<script>
$(document).ready(function(){
';

while ($x < $screens + 1) {
    $content .= '
        $("#fileShot'.$x.'").on("change", function() {
            uploadShot('.$x.');
        });
    ';
    $x++;
}
$content .= <<<HTML
});

function uploadShot(fr) {
        if ($("#fileFrame" + fr).val() === "") {
        } else {
            $("#frameForm" + fr).ajaxForm({
                success: function(e) {
                    if (e === "ERROR") {
                        swal("Error", e, "error");
                    } else {
//                        $("#fP" + fr).attr("style", "height:80%;");
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
 
 function saveShots(id , screens , d) {      
    var bbb = $("#SELECTEDBOOTH").val();
    var ajaxData = {id: id , screens : screens , cDate : d};
    $.ajax({
        url: 'edit/functions/events/saveShots.php',
        type: 'POST',
        success: function(data) {

            if (data === "OK") {
                hidePopupv2();
                closePopup();
                profile("events", "photobooths", {$eventID});
                var f = d + "" + id;
                var a = '{$boothType}';
                setTimeout(function(){
                    $("#setString" + id).val("3");
                    canviaApartat2(id , a , f , 3 ,bbb );
                } , 1500);
            }
            else{
                swal("Error", data, "error");
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
