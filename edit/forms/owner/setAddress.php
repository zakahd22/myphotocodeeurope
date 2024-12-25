<?php
$title = "";
$content = "";
$buttons = "";

$CLD_CON->OpenRs("SELECT * FROM App_ownerAddress WHERE id= $ID");
if ($CLD_CON->FetchArray()) {
    $idOwner = $CLD_CON->GetArrayField("idOwner");
    $address = $CLD_CON->GetArrayField("address");
    $state = $CLD_CON->GetArrayField("state");
    $zipCode = $CLD_CON->GetArrayField("code");
    $country = $CLD_CON->GetArrayField("country");
    $city = $CLD_CON->GetArrayField("city");
    $contact = $CLD_CON->GetArrayField("CLD_contactName");
    $company = $CLD_CON->GetArrayField("CLD_companyName");
    $phone = $CLD_CON->GetArrayField("CLD_phone");
    
    if($type==0){
        $title = "Edit Business Address ";
    }
    else{
        $title = "Edit Shipping Address";
    }
    
    $content .= "<div class='popup-text'>";
    $content .= "Change the fields that you want to update.<br/>";
    $content .= "The shipping address will not be available immediately.";
    $content .= "</div>";
    
    $content .= "<form id='addressForm' onsubmit='return false;'>";
    
    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "Company Name: <input type='text' value='$company' class='popupInputLarge' id='companyName'>";
        $content .= "</div>";   
        $content .= "<div class='popup-col'>";
            $content .= "Address : <input type='text' value='$address' class='popupInputLarge' id='street'>";
        $content .= "</div>";
    $content .= "</div>";

    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "City : <input type='text' value='$city' class='popupInputLarge' id='city'>";
        $content .= "</div>";
        $content .= "<div class='popup-col'>";
            $content .= "Zip code : <input type='text' value='$zipCode' class='popupInputLarge' id='zip'>";
        $content .= "</div>";
    $content .= "</div>";

    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "State :   <input type='text' value='$state' class='popupInputLarge' id='state'>";
        $content .= "</div>";
        $content .= "<div class='popup-col'>";
            $content .= "Country :   <input type='text' value='$country' class='popupInputLarge' id='country'>";
        $content .= "</div>";
    $content .= "</div>";

    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "Contact Name : <input type='text' value='$contact' class='popupInputLarge' id='contactName'>";
        $content .= "</div>";
        $content .= "<div class='popup-col'>";
            $content .= "Phone : <input type='text' value='$phone' class='popupInputLarge' id='phone'>";
        $content .= "</div>";
    $content .= "</div>";
    
    $content .= "</form>";
    
    $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='editAddress($ID); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";
}

$content .= <<<HTML
<script>
    function editAddress(id){
        var street = $("#street").val();
        var city = $("#city").val();
        var state = $("#state").val();
        var zip = $("#zip").val();
        var country = $("#country").val();
        var companyName = $("#companyName").val();
        var phone = $("#phone").val();
        var contactName = $("#contactName").val();
        //alert(street + " " + city + " " + country + " " + state + " " + zip);
        var ajaxData = {street: street, city: city, state: state, zip: zip, country: country, id: id ,contact: contactName , company : companyName , phone : phone};

        $.ajax({
            url: 'edit/functions/owner/setAddress.php',
            type: 'POST',
            //Ajax events
            success: function(data){
                if (data === "OK"){
                    closePopup();
                    profile("owner", "addresses", $idOwner);
                } 
                else {
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