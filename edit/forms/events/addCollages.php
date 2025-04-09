<?php

$content .= '<link rel="stylesheet" type="text/css" href="sections/templates/resources/css/printCollage.css">';
//$content .= '<script type="text/javascript" src="sections/events/resources/js/printPhotos.js"></script>';

$title = "New Custom Collages";

$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "<div class='Expression'>File type: PNG, File size: 2044x1416</div>";
$content .= "<div class='Britta'>Resolucion: 300dpi.</div>";
$content .= "(PNG Files)";
$content .= "</div>";

$content .= "<div style='margin-top:10px'>";

//FRAME1
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm1' style='margin-right: -64px;' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage1' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='1' name='collage'>";
$content .= "lay1";
$content .= "<img style='width:40px; margin-left: 10px;' src='images/web/lay1.png'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP1'>";
$content .= "</div>";

$content .= "</div>";

//FRAME2
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm2' style='margin-right: -64px;' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage2' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='2' name='collage'>";
$content .= "lay2";
$content .= "<img style='width:40px; margin-left: 10px;' src='images/web/lay2.png'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP2'>";
$content .= "</div>";

$content .= "</div>";

//FRAME3
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm3' style='margin-right: -64px;' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage3'accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='3' name='collage'>";
$content .= "lay3";
$content .= "<img style='width:40px; margin-left: 10px;' src='images/web/lay3.png'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP3'>";
$content .= "</div>";

$content .= "</div>";

//FRAME4
$content .= "<div class='collagesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='collageForm4' style='margin-right: -64px;' action='edit/functions/events/uploadCollage.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileCollages' id='fileCollage4' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='4' name='collage'>";
$content .= "lay4";
$content .= "<img style='width:40px; margin-left: 10px;' src='images/web/lay4.png'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='collagePreview2' id='fP4'>";
$content .= "</div>";

$content .= "</div>";

$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveCollages($ID); '>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML

<script>
    var f1 = false;
    var f2 = false;
    var f3 = false;
    var f4 = false;
    $(document).ready(function() {
        $("#fileCollage1").on("change", function() {
            uploadCollage(1);
        });
        $("#fileCollage2").on("change", function() {
            uploadCollage(2);
        });
        $("#fileCollage3").on("change", function() {
            uploadCollage(3);
        });
        $("#fileCollage4").on("change", function() {
            uploadCollage(4);
        });
    });

    function uploadCollage(fr) {
        if ($("#fileCollage" + fr).val() === "") {
        } else {
            $("#collageForm" + fr).ajaxForm({
                success: function(e) {
                    if (e === "ERROR") {
                        alert("Error");
                    } else {
                        $("#fP" + fr).html("<img src='printPhoto/tmp/" + e + "' style='width:100%; height:100%;'>");
                        $("#fP" + fr).show(500);
                        switch (fr) {
                            case 1:
                                f1 = true;
                                break;
                            case 2:
                                f2 = true;
                                break;
                            case 3:
                                f3 = true;
                                break;
                            case 4:
                                f4 = true;
                                break;
                        }
                    }
                },
                error: function(e) {

                }
            });
            $("#collageForm" + fr).submit();

        }
    }

    function saveCollages(id) {
        if (f1 && f2 && f3 && f4) {
            var ajaxData = {id: id};
            $.ajax({
                url: 'edit/functions/events/saveCollages.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    if (data === "OK") {
                        closePopup();
                        profile("events", "printPhoto", id);
                    }
                    $("#selectedCollage").html(data);
                    hidePopupv2();
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
            $("#selectedCollage").addClass("show");
            $("#selectedCollage").removeClass("hidden");
        } else {
            alert("Need 4 Collages");
        }
    }

</script>

HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);