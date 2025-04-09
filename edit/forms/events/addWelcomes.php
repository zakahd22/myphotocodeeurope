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
$welcome_width = $CLD_CON->GetArrayField('welcome_w');
$welcome_height = $CLD_CON->GetArrayField('welcome_h');
$screens = $CLD_CON->GetArrayField('screens');


$title = "Welcomes";
$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "Upload $screens image/s. File type: JPG , File size:". $welcome_width." x ".$welcome_height." (pixels)";
$content .= "</div>";
$x = 1;

while ($x < $screens + 1) {
    $content .= "<div class='framesADD'>";
    $content .= "<div class='upload-dos'>";
    $content .= "<form id='frameForm$x' action='edit/functions/events/uploadWelcome.php' enctype='multipart/form-data'>";
    $content .= "<input type='file' class='popup-input-large' name='fileWelcome' id='fileWelcome$x' accept='image/jpeg'>";
    $content .= "<input type='hidden' value='$ID' name='id'>";
    $content .= "<input type='hidden' value='$x' name='welcome'>";
    $content .= "<input type='hidden' name='hWel' value='$welcome_height'>";
    $content .= "<input type='hidden' name='wWel' value='$welcome_width'>";
    $content .= "</form>";
    $content .= "</div>";
    $content .= "<div class='framePreview' id='fP$x'>";
    $content .= "</div>";    
    $content .= "</div>";
    $x++;
}
$content .= "</div>";
$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveWelcomes($ID , $screens , $creationDate);'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";


$x=1;

$content .= '<script>
$(document).ready(function(){
';
while ($x < $screens + 1) {
    $content .= '$("#fileWelcome'.$x.'").on("change", function() {
         uploadWelcome('.$x.');
        });
    ';
$x++;
}
$content .= "});";
$content .= '
    function uploadWelcome(fr) {
            if ($("#fileFrame" + fr).val() === "") {
            } else {
                $("#frameForm" + fr).ajaxForm({
                    success: function(e) {
                        if (e === "ERROR") {
                            swal("Error", "", "error");
                        } else {
                            $("#fP" + fr).html("<img src=\'printPhoto/tmp/" + e + "\' style=\'width:100%; margin-left:10px; margin-top:1px;\'>");
                            $("#fP" + fr).show(500);
                        }
                    },
                    error: function(e) {

                    }
                });
                $("#frameForm" + fr).submit();

            }
     }

     function saveWelcomes(id , screens , d) {     
                var bbb = $("#SELECTEDBOOTH").val();
                var ajaxData = {id: id , screens : screens , cDate : d};
                $.ajax({
                    url: \'edit/functions/events/saveWelcomes.php\',
                    type: \'POST\',
                    success: function(data) {
                        if (data === "OK") {
                            hidePopupv2();
                            closePopup();
                            profile("events", "photobooths", '.$eventID.');
                            var f = d + "" + id;
                            var a = \''.$boothType.'\';
                            setTimeout(function(){
                                $("#setString" + id).val("1");
                                canviaApartat2(id , a , f , 1 , bbb);
                            } , 1500);

                        }else{
                            swal("Error","Please select the " + screens + " welcome screens.", "error");
                        }
                    },
                    data: ajaxData,
                    contentType: \'application/x-www-form-urlencoded\'
                });
     } 

    </script>';

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);
