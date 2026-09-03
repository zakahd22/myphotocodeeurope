<?php
$ID = $_POST['id'];
$title = "Profile Image";
$content = "";
$buttons = "";

$content .= "<div class='popup-center'>";
    $content .= "Select your jpg image.";
$content .= "</div>";
$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<form id='imageForm' method='POST' action='edit/functions/owner/uploadPreImg.php' enctype='multipart/form-data'>";
    $content .= "<input type='file' name='profileIMG' id='myPhoto2' accept='image/jpeg' class='popupInputLarge'>";
    $content .= "<input type='hidden' value='$ID' name='id'>";
    $content .= "</form>";
$content .= "</div>";
$content .= "<div class='popup-row'>";
    $content .= "<div class='preview'></div>";
$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveImgOwner($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    $(document).ready(function() {
        $("#myPhoto2").on("change", function() {
            if ($("#myPhoto2").val() === "") {} 
            else {
                $("#imageForm").ajaxForm({
                    beforeSend: function() {

                    },
                    success: function(e) {
                        if(e === "ERROR") {
                            alert("Error");
                        } 
                        else {
                            $(".preview").html("<img src='images/ownerIMG/tmp/" + e + "' style='width:35%; height:auto;'/>");
                            $(".preview").show(500);
                        }
                    },
                    error: function(e) {

                    }
                });
                $("#imageForm").submit();

            }
        });
    });
    function saveImgOwner(id) {
        var ajaxData = {id: id};
        $.ajax({
            url: 'edit/functions/owner/saveImgOwner.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              //alert(data);
              if(data==="OK"){
                  closePopup();
                  profile("owner" , "info" , id);
              }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);