<?php

$baseController->createModel('rentals');

$rental = $baseController->rentalsModel->getRentalsNames($ID);
$name='';
if($rental){
    $username = $rental[0]["username"];
}

$title = "Username";
$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "Type on the text box the new username.";
$content .= "</div>";
$content .= "<input type='text' class='popupInputLarge popup-margin-top' value='$username' id='uName'>";
$buttons .= "<input type='button' class='popup-confirm' value='Save'  onClick='setUserName($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";


$content .= <<<HTML
<script>
    function setUserName(id){
       var name = $("#uName").val();
       name = name.trim();
       if(name.length>0){
        var ajaxData = {id: id , n : name};
        $.ajax({
            url: 'edit/functions/owner/setUserName.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  closePopup();
                  profile("owner" , "info" , id);
              }else{
                  alert(data);
              }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
        }else{
            alert("The name is empty");
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);