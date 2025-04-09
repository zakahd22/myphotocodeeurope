<?php

$baseController->createModel('events');

$events = $baseController->eventsModel->getEvent($ID);
if($events){
    $private = $events[0]["private"];
}

$title = "Privacy";

$content = "";
$buttons = "";
$content .= "<div class='popup-text'>";
$content .= "If the event is private, the customers will only be able to see their photo.<br/><br/>";
$content .= "If the event is public, they will be able to see all the photos taken at the event.";
$content .= "</div>";

if($private == 1){
    $content .= "<h3>This event is PRIVATE</h3>";
    $buttons .= "<input type='button' class='popup-confirm' value='Set as PUBLIC' onclick='setPrivacity($ID , 0); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
}else{
    $content .= "<h3>This event is PUBLIC</h3>";
    $buttons .= "<input type='button' class='popup-confirm' value='Set as PRIVATE' onclick='setPrivacity($ID , 1); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$content .= <<<HTML
<script>
    function setPrivacity(id , private){
        var ajaxData = {id: id , private:private};
        $.ajax({
            url: 'edit/functions/events/setPrivate.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  closePopup();
                  profile("events" , "cloud" , id);
              }else{
                  alert("ERROR");
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