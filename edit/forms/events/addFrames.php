<?php
$title = "New Custom Frames";

$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "<div class='Expression'>File type: PNG, File size: 1280x960</div>";
$content .= "<div class='Britta'>Resolucion: 300dpi.</div>";
$content .= "(PNG Files)";
$content .= "</div>";

$content .= "<div style='margin-top:10px'>";

//FRAME1
$content .= "<div class='framesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='frameForm1' action='edit/functions/events/uploadFrame.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileFrames' id='fileFrame1' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='1' name='frame'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='framePreview2' id='fP1'>";
$content .= "</div>";

$content .= "</div>";

//FRAME2
$content .= "<div class='framesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='frameForm2' action='edit/functions/events/uploadFrame.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileFrames' id='fileFrame2' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='2' name='frame'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='framePreview2' id='fP2'>";
$content .= "</div>";

$content .= "</div>";

//FRAME3
$content .= "<div class='framesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='frameForm3' action='edit/functions/events/uploadFrame.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileFrames' id='fileFrame3' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='3' name='frame'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='framePreview2' id='fP3'>";
$content .= "</div>";

$content .= "</div>";

//FRAME4
$content .= "<div class='framesADD'>";
$content .= "<div style='padding-right:10px'>";
$content .= "<form id='frameForm4' action='edit/functions/events/uploadFrame.php' enctype='multipart/form-data'>";
$content .= "<input type='file' class='popup-input-large' name='fileFrames' id='fileFrame4' accept='.png'>";
$content .= "<input type='hidden' value='$ID' name='id'>";
$content .= "<input type='hidden' value='4' name='frame'>";
$content .= "</form>";
$content .= "</div>";
$content .= "<div class='framePreview2' id='fP4'>";
$content .= "</div>";

$content .= "</div>";

$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveFrames($ID); '>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();' >";

$content .= <<<HTML

<script>
    var f1 = false;
    var f2 = false;
    var f3 = false;
    var f4 = false;
    $(document).ready(function() {
        $("#fileFrame1").on("change", function() {
            uploadFrame(1);
        });
        $("#fileFrame2").on("change", function() {
            uploadFrame(2);
        });
        $("#fileFrame3").on("change", function() {
            uploadFrame(3);
        });
        $("#fileFrame4").on("change", function() {
            uploadFrame(4);
        });
    });

    function uploadFrame(fr) {
        if ($("#fileFrame" + fr).val() === "") {
        } else {
            $("#frameForm" + fr).ajaxForm({
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
            $("#frameForm" + fr).submit();

        }
    }

    function saveFrames(id) {
        if (f1 && f2 && f3 && f4) {
            var ajaxData = {id: id};
            $.ajax({
                url: 'edit/functions/events/saveFrames.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    $("#selectedFrames").html(data);
                    hidePopupv2();
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
            $("#selectedFrames").addClass("show");
            $("#selectedFrames").removeClass("hidden");
        } else {
            alert("Need 4 Frames");
        }
    }

</script>

HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);