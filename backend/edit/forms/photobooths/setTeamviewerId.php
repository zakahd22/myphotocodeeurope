<?php

$baseController->createModel('App_booths');

$booth = $baseController->App_boothsModel->getBoothWhereid($ID);
$teamviewerId = $booth[0]["PBtwid"];

$title = "Teamviwer ID";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Type on the text box the new Teamviwer ID.";
$content .= "</div>";

$content .= "<div class='popup-text'>";
$content .= "<input type='text' class='popupInputLarge' value='$teamviewerId' id='tw'>";
$content .= "</div>";

$buttons .= "<button type='button' class='popup-confirm' onClick='setTW($ID);hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function setTW(id){
       var tw = $("#tw").val();
       tw = tw.trim();  
        var ajaxData = {id: id , tw : tw};
        $.ajax({
            url: 'edit/functions/photobooths/setTeamviwerId.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  closePopup();
                  profile("photobooths" , "info" , id);
              }else{
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