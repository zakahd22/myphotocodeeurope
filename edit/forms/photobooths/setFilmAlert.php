<?php

$baseController->createModel('App_alerts');
$baseController->createModel('App_boothAlertDef');


$typeAlert = 11;
$alerts = $baseController->App_alertsModel->getAlertsByTypeAlert($typeAlert);

if($alerts){
   $f = $alerts[0]["values"]; 
}

$values = explode("#", $f);

$title = "Film Alert";
$content = "";
$val = "none";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";

$boothAlertDef = $baseController->App_boothAlertDefModel->getAlerts($ID, 11);

if($boothAlertDef){
    $val = $boothAlertDef[0]["value"];
    if ($val == "none") {
        $content .= "The film alert is not active. <br/>";
    } else {
        $content .= "When the film stock falls below $val units, you will receive an alert. <br/>";
    }
}
else {
    $content .= "The film alert is not active.<br/>";
}
$content .= "Select a value to receive an alert when the film falls below this value.<br/>";
$content .= "</div>";

$x = 0;
$content .= "<select class='popupInputLarge' onchange='toCheckInput();' id='filmVal'>";
while ($x < sizeof($values)) {
    $v = $values[$x];
    $v2 = $values[$x + 1];
    if ($v == $val) {
        $content .= "<option value='$v' selected>$v2</option>";
    } else {
        $content .= "<option value='$v'>$v2</option>";
    }
    $x = $x + 2;
}

$content .= "</select>";
if($val == "none"){
    $buttons .= "<input type='button' class='popup-confirm' value='Turn ON'  onclick='setFilmAlert($ID);'>";
}
else {
    $buttons .= "<input type='button' class='popup-confirm' id='buttonCancel' value='Accept'  onclick='setFilmAlert($ID); hidePopupv2();'>";
}

$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function toCheckInput(){
        if($("#filmVal").val() != "none"){
            $("#buttonCancel").css("background-color","#3085d6");
            $("#buttonCancel").val("Save");
        }
        else {
            $("#buttonCancel").css("background-color","#dd6b55");
            $("#buttonCancel").val("Turn OFF");
        }
    }
        
    function setFilmAlert(id){
        var value =  $("#filmVal").val();
        var ajaxData = {value : value, id: id};
        $.ajax({
            url: 'edit/functions/photobooths/setFilmAlert.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.status === "OK") {
                    closePopup();
                    hidePopupv2();
                    profile("photobooths", "alerts", id);
                } else {
                    swal('Error', data.message, 'error');
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