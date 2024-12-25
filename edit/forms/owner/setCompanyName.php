<?php

$baseController->createModel('rentals');

$rental = $baseController->rentalsModel->getRentalsNames($ID);

if($rental){
    $name = $rental[0]["name"];
}

$title = "Company Name";
$content = "";
$buttons = "";

$content .= "<div class='popup-text'>Type on the text box the new company name.</div>";
$content .= "<input type='text' class='popupInputLarge popup-margin-top' value='$name' id='cName'>";
$buttons .= "<input type='button' class='popup-confirm' value='Save'  onClick='setCompanyName($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function setCompanyName(id){
       var name = $("#cName").val();
       name = name.trim();
       if(name.length>0){
        var ajaxData = {id: id , n : name};
        $.ajax({
            url: 'edit/functions/owner/setCompanyName.php',
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
            alert("The email is not correct");
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);