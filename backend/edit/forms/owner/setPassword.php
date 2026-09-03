<?php
$ID = $_POST['id'];

$title = "New Password";
$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "To set your password, type in the current one, then enter the new one twice to make sure that there is no mistake.";

$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col' style='width:35%;'>";
        $content .= "Current password:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='password' class='popupInputLarge' id='pwd'>";
    $content .= "</div>";
$content .= "</div>";
$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col' style='width:35%;'>";
        $content .= "New Password:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='password' class='popupInputLarge' id='pwd1'>";
    $content .= "</div>";
$content .= "</div>";
$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col' style='width:35%;'>";
        $content .= "Repeat new password:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='password' class='popupInputLarge' id='pwd2'>";
    $content .= "</div>";
$content .= "</div>";

$content .= "</div>";
$buttons .= "<input type='button' class='popup-confirm' value='Save'  onclick='setOwnerPassword($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function setOwnerPassword(id) {
        var p = $("#pwd").val();
        var p1 = $("#pwd1").val();
        var p2 = $("#pwd2").val();
        if (p.length == 0 || p1.length == 0 || p2.length == 0) {
            alert("All fields are required");
        } else {
            if (p1 != p2) {
                alert("The passwords do not match");
            } else {
                var ajaxData = {id: id, p: p, p1: p1};
                $.ajax({
                    url: 'edit/functions/owner/saveOwnerPassword.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {
                            alert("The password is changed succesfully");
                            closePopup();
                            profile("owner", "info", id);
                        } else {
                            alert(data);
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }
        }
    }
</script>
HTML;
$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);