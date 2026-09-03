<?php

$baseController->createModel('events');

$events = $baseController->eventsModel->getEvent($ID);

if ($events) {
    $title2= $events[0]["title"];
}

$title = "Event title";
$content = "";
$buttons = "";
$content .= "<div class='popup-text'>";
$content .= "The actual title is:<br/><br/><b><center>$title2</center></b><br/><br/>";
$content .= "Type the new title in the field below."; 
$content .= "</div>";
$content .= "<input type='text' class='popupInputLarge' style='margin-top:5px;' value='$title2' id='titleEvent'>";
$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='setTitleEvent($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function setTitleEvent(id){
        var title = $("#titleEvent").val();
        var ajaxData = {id: id , title : title};
        $.ajax({
            url: 'edit/functions/events/setTitleEvent.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("events", "cloud", id);
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