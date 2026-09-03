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

$title = "";
$content = "";
$buttons = "";

$CLD_CON->OpenRs("SELECT * FROM CLD_Contactes WHERE id= $ID");

if ($CLD_CON->FetchArray()) {
    $contacte_id = $CLD_CON->GetArrayField("id");
    $name = $CLD_CON->GetArrayField("name");
    $surnames = $CLD_CON->GetArrayField("surnames");
    $phone = $CLD_CON->GetArrayField("phone");
    $email = $CLD_CON->GetArrayField("email");
    $jobRank = $CLD_CON->GetArrayField("carrec");
    $zonaHoraria = $CLD_CON->GetArrayField("city");
    $owner = $CLD_CON->GetArrayField("rental_id");
    
    $title .= "Edit Contact";
    
    $content .= "<div class='popup-text'>Change the fields that you want to update.</div>";
    //$content .= "<form id='contactForm' onsubmit='return false;'>";
    
    $content .= "<div class='popup-row popup-margin-top'>";
        $content .= "<div class='popup-col'>";
            $content .= "First Name:";
            $content .= "<input type='text' value='$name' name='nom' id='nom' class='popupInputLarge'>";
        $content .= "</div>";
        $content .= "<div class='popup-col'>";
            $content .= "Last Name:";
            $content .= "<input type='text' value='$surnames' name='cognom' id='cognom' class='popupInputLarge'>";
        $content .= "</div>";
    $content .= "</div>";
    
    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "E-mail:";
            $content .= "<input type='text' value='$email' name='mail' id='mail' class='popupInputLarge'>";
        $content .= "</div>";
        $content .= "<div class='popup-col'>";
            $content .= "Phone:";
            $content .= "<input type='text' value='$phone' name='phone'  id='phone' class='popupInputLarge'>";
        $content .= "</div>";
    $content .= "</div>";
    
    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "Job Title:";
            $content .= "<input value='$jobRank' type='text' name='job' id='job' class='popupInputLarge'>";
        $content .= "</div>";
    $content .= "</div>";
    
    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "Time Zone:";
            $content .= "<select class='popupInputLarge' id='timezone'>";
                $content .= "<option value=\"0\">Please, select timezone</option>";
                foreach (tz_list() as $t) {
                    $checked = "";
                    if ($t['zone'] == $zonaHoraria) {
                        $checked = 'selected';
                    }
                    $content .= "<option value=".$t['zone']." ".$checked.">".$t['diff_from_GMT']." - ".$t['zone']."</option>";
                }
            $content .= "</select>";
        $content .= "</div>";
    $content .= "</div>";
    //$content .= "</form>";
}
    $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='editContact($ID); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";
$content .= <<<HTML
<script>
    function editContact(id) {
        var name = $("#nom").val();
        var lastName = $("#cognom").val();
        var email = $("#mail").val();
        var phone = $("#phone").val();
        var job = $("#job").val();
        var timezone = $("#timezone").val();
        var ajaxData = {name : name , lastName: lastName, email: email , phone : phone , job : job ,timezone : timezone , id:id};
        $.ajax({
            url: 'edit/functions/owner/setContact.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("owner", "contacts", $owner);
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