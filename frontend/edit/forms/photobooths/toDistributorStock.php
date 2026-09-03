<?php
$CLD_CON->OpenRs("SELECT serialnumber, CLD_Status FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $status = $CLD_CON->GetArrayField("CLD_Status");
}

$title = "Change PhotoBooth $sn to Stock";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Select which distributor should receive this photobooth:";
$content .= "</div>";

// Add the distributor dropdown
$content .= "<div class='popup-margin-bottom'>";
$content .= "<select id='distributor' class='popup-select'>";
$content .= "<option value='0'>-- Select Distributor --</option>";

// Get list of distributors
$CLD_CON->OpenRs("SELECT id, Name, LOCATION FROM CLD_Distributors ORDER BY id");
while ($CLD_CON->FetchArray()) {
    $disID = $CLD_CON->GetArrayField("id");
    $disName = $CLD_CON->GetArrayField("Name");
    $disLocation = $CLD_CON->GetArrayField("LOCATION");
    $content .= "<option value='$disID'>$disName - $disLocation</option>";
}
$content .= "</select>";
$content .= "</div>";

$buttons .= "<button type='button' class='popup-confirm' onclick='toDistributor($ID, $status);'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toDistributor(id, status){
        var dis = $("#distributor").val();
        if(dis === "0"){
            alert("Please select a distributor");
        }else{
            var ajaxData = {dis: dis, id: id, from: status};
            $.ajax({
                url: 'edit/functions/photobooths/toDistributor.php',
                type: 'POST',
                beforeSend: function(){loadPopUp();},
                success: function(data) {
                    unloadingPopUp();
                    if (data === "OK") {
                        hidePopupv2();
                        profile("photobooths", "info", id);
                    } else {
                        alert(data);
                    }
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' => $content, 'buttons' => $buttons);
echo json_encode($array_result);
?>