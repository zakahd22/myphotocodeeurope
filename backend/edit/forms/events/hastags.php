<?php
require_once G_PATH . "common/Classes/baseController.php";
$baseController = new baseController();
$baseController->createModel('events');

$hastag1=$baseController->eventsModel->getEvent($ID);
if ($hastag1) {
    $hastag1= $hastag1[0]["hashtag"];
}

$content = "";
$buttons = "";

$title = "Hastags";
$content .= "<div class='popup-text'>";
$content .= "<p>These hashtags will appear in the text when sharing your photos and videos from this event.</p>";
$content .= "<p>Type the hastags separated by a space (#hashtag1 #hashtag2 #hashtag3 ...)</p>"; 
$content .= "</div>";
$content .= "<input type='text' class='popup-input-large' value='$hastag1' id='hashtag1234'>";
$content .= <<<HTML
    <script>
        function setHashtagEvent(id){
            var hashtag = $("#hashtag1234").val();
            var ajaxData = {hashtag : hashtag , id: id};
            $.ajax({
                url: 'edit/functions/events/setHastag.php',
                type: 'POST',
                //Ajax events
                success: function(data) {
                    if (data === "OK") {
                        closePopup();
                        profile("events", "info", id);
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

$buttons .= "<button type='button' class='popup-confirm' onclick='setHashtagEvent({$ID}); hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";
    
$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);