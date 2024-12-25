<?php
$title = "New Production";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Select the kind of Photobooth and number for this production.";
$content .= "</div>";

$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col'>";
        $content .= "PhotoBooth type";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<select id='boothType' class='popupInputLarge'>";
            $content .= "<option value=0> ---------------------</option>";
            $CLD_CON->OpenRs("SELECT id , name FROM CLD_boothTypes");
            while($CLD_CON->FetchArray()){
                $nom = $CLD_CON->GetArrayField("name");
                $id = $CLD_CON->GetArrayField("id");
                $content .= "<option value='$id'> $nom </option>";
            }
        $content .= "</select>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col'>";
        $content .= "Numbers PhotoBooths";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='number' class='popupInputLarge' min=0 value='0' id='num'>";
    $content .= "</div>";
$content .= "</div>";

$buttons .= "<button type='button' class='popup-confirm' onclick='addProduction(); hidePopupv2();'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function addProduction(){
        var tipus = $("#boothType").val();
        var num = $("#num").val();
        if(tipus == 0 ){
            swal("Choose a type of Photobooth");
            return;
        }
         if(num < 1 ){
            swal("The number of PBs must be more than zero");
            return;
        }
        var ajaxData = {type: tipus , n : num};
        $.ajax({
            url: 'edit/functions/productions/newProduction.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {                           
                    closePopup();
                    profile("photobooths" , "incidents", {$id});
                } else {
                    swal(data);
                }
            },
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);