<?php
    $title = "Generate User";
    $content .= "<div class='popup-text'>";
    $content .= "This owner does not have username neither password, to create it click Accept.<br/>";
    $content .= "If the user has set the alert email, he will receive notification with his username and password.";
    $content .= "</div>";
    $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='generateUser(); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
$content .= <<<HTML
<script>
    function generateUser(){  
       var owner=$ID;
        var ajaxData = {own : owner};
        $.ajax({
            url: 'edit/functions/owner/generateUser.php',
            type: 'POST',
            success: function(data) {
              if (data === "OK") {
                    profile("owner", "info", owner);
                    closePopup();
                } else {
                    alert(data);
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