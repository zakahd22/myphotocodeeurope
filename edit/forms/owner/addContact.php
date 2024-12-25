<?php

function tz_list() {
    $zones_array = array();
    $timestamp = time();
    foreach (timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zones_array[$key]['zone'] = $zone;
        $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }
    return $zones_array;
}
$title = "New Contact";
$content = "";
$buttons = "";

$content .= "<input type='hidden' name='owner' id='owner1234' value=".$ID.">";
$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "First Name:";
        $content .= "<input type='text' name='nom' id='nom' class='popupInputLarge'>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "Last Name:";
        $content .= "<input type='text' name='cognom' id='cognom' class='popupInputLarge'>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "E-mail:";
        $content .= "<input type='text' name='mail' id='mail' class='popupInputLarge'>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "Phone:";
        $content .= "<input type='text' name='phone'  id='phone' class='popupInputLarge'>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "Job Title:";
        $content .= "<input type='text' name='job' id='job' class='popupInputLarge'>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col'>";
        $content .= "Time Zone:";
        $content .= "<select class='popupInputLarge' id='timezone'>";
            $content .= "<option value=\"0\">Please, select timezone</option>";
            foreach (tz_list() as $t) {
                $content .= "<option value=".$t['zone'].">".$t['diff_from_GMT']." - ".$t['zone']."</option>";
            }
        $content .= "</select>";
    $content .= "</div>";
$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='addContact(); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function addContact() {
        var name = $("#nom").val();
        var lastName = $("#cognom").val();
        var email = $("#mail").val();
        var phone = $("#phone").val();
        var job = $("#job").val();
        var timezone = $("#timezone").val();
        var owner = $("#owner1234").val();
        var ajaxData = {name : name , lastName: lastName, email: email , phone : phone , job : job ,timezone : timezone , owner :owner};

        $.ajax({
            url: 'edit/functions/owner/addContact.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("owner", "contacts", owner);
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