<?php

$baseController->createModel('App_booths');

$pb = $baseController->App_boothsModel->getBoothWhereid($ID);
if($pb){
    $paypalID = $pb[0]["payPalVendor"];
}

$title = "PayPal Merchant account ID";
$content = "";
$buttons = "";

$content .= "The Merchant account ID was developed as a substitute for your email address to prevent spam <br /> bots from harvesting your email address on web site pages that contain your item button code.<br/>";
$content .= "The Merchant account ID is sometimes referred to as your PayPal Account ID Number.<br/>";
$content .= "Type the Paypal account ID in the field below.<br/><br/>";

if($paypalID){
    $content .= "<br/><br/><center>To turn OFF the account, erase the field below.</center>";
}

$content .= "<input type='text' class='popupInputLarge popup-margin-top popup-margin-bottom' onkeyup='toCheckInput()' style='width:auto;' value='$paypalID' id='paypalID'>";


if($paypalID){
    $buttonText = "Save";
    $buttons .= "<button type='button' class='popup-confirm' id='buttonCancel' onClick='setPayPal($ID); hidePopupv2();'>$buttonText</button>";

} else {
    $buttonText = "Turn ON";
    $buttons .= "<button type='button' class='popup-confirm'  onClick='setPayPal($ID); hidePopupv2();'>$buttonText</button>";

}

$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toCheckInput(){
        if($("#paypalID").val() != ""){
            $("#buttonCancel").css("background-color","#3085d6");
            $("#buttonCancel").html("Save");
        }
        else {
            $("#buttonCancel").css("background-color","#dd6b55");
            $("#buttonCancel").html("Turn OFF");
        }
    }
        
    function setPayPal(id){
      var paypal = $("#paypalID").val();     
      var ajaxData = {paypal: paypal , id: id};
        $.ajax({
            url: 'edit/functions/photobooths/setPayPalID.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {
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