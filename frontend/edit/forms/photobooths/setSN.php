<?php
$baseController->createModel('App_booths');

$booth = $baseController->App_boothsModel->getBoothWhereid($ID);
$serialNumber = $booth[0]["serialnumber"];

$title = "Serial Number";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Type on the text box the new serialnumber.";
$content .= "</div>";

$content .= "<div class='popup-text'>";
$content .= "<input type='text' class='popupInputLarge' value='$serialNumber' id='sn'>";
$content .= "</div>";

$buttons .= "<button type='button' class='popup-confirm' onClick='setSN($ID);hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function setSN(id){
       var sn = $("#sn").val();
       sn = sn.trim();      
        var ajaxData = {id: id , sn : sn};
        $.ajax({
            url: 'edit/functions/photobooths/setSN.php',
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