<?php
$content = "";
$buttons = "";
if($type==1){
    $title = "New Business Address ";
}
else{
    $title = "New Shipping Address";    
    $content .= "<div class='popup-text'>The shipping address will not be available immediately.</div>";   
}
$content .= "<form id='addressForm' onsubmit='return false;'>";

$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col'>";
        $content .= "Company Name (*): <input type='text' value='' class='popupInputLarge' id='companyName' required>";
    $content .= "</div>";   
    $content .= "<div class='popup-col'>";
        $content .= "Address (*): <input type='text' value='' class='popupInputLarge' id='street' required>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "City (*): <input type='text' value='' class='popupInputLarge' id='city' required>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "Zip code (*): <input type='text' value='' class='popupInputLarge' id='zip' required>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "State :   <input type='text' value='' class='popupInputLarge' id='state'>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "Country (*):   <input type='text' value='' class='popupInputLarge' id='country' required>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "Contact Name (*): <input type='text' value='' class='popupInputLarge' id='contactName' required>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "Phone (*): <input type='text' value='' class='popupInputLarge' id='phone' required>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<input type='hidden' id='addressType' name='type' value='$type'>";
$content .= "<input type='hidden' id='owner1234' value='$ID'>";
$content .= "</form>";

$buttons .= "<button class='popup-confirm' onclick='addAddress();'>Save</button>";
$buttons .= "<button class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function addAddress() {
        var street = $("#street").val();
        var city = $("#city").val();
        var state = $("#state").val();
        var zip = $("#zip").val();
        var country = $("#country").val();
        var t = $("#addressType").val();
        var owner = $("#owner1234").val();
        var companyName = $("#companyName").val();
        var phone = $("#phone").val();
        var contactName = $("#contactName").val();
        if(owner.length ===0){
            alert("Owner is empty , "  + owner);
        }
        var ajaxData = {own : owner , street: street, city: city, state: state, zip: zip, country: country , type : t , contact: contactName , company : companyName , phone : phone};

        $.ajax({
            url: 'edit/functions/owner/addAddress.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
HTML;
                    if(isset($to)){
                        $content .= <<<HTML
                        hidePopupv2();
                        profile("owner", "contacts", owner);           
                        edit(36 , owner);
HTML;
                    } else{
                        $content .= <<<HTML
                        //closePopup();
                        hidePopupv2();
                        profile("owner", "addresses", owner);
HTML;
                    }
$content .= <<<HTML
                } 
                else {
                    $(".swal2-validationerror").show();
                    $(".swal2-validationerror").html(data);
                    //alert(data);
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