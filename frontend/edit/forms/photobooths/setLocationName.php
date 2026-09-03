<?php

$baseController->createModel('App_booths');

$pb = $baseController->App_boothsModel->getBoothWhereid($ID);
$loc = "";
if($pb){
    $loc = $pb[0]["location"];
}

$title = "Location Name";

$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "The location name is where the PhotoBooth is placed.<br/>";
    $content .= "If you want to change the PhotoBooth’s location name, type the new location in the box below.";
$content .= "</div>";

$content .= "<input type='text' class='popupInputLarge popup-margin-top' value='$loc' id='locName'>";

$buttons .= "<button type='button' class='popup-confirm' onClick='setLocationName($ID); hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function setLocationName(id){
        var locName = $("#locName").val(); 
        var ajaxData ={loc : locName , id : id};
         $.ajax({
            url: 'edit/functions/photobooths/setLocationName.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK"){
                    closePopup();
                    profile("photobooths", "info", id);
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