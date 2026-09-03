<?php

$baseController->createModel('App_alerts');
$baseController->createModel('App_boothAlertDef');

$alerets = $baseController->App_alertsModel->getAlertsByTypeAlert(12);

if($alerets){
    $f = $alerets[0]["values"];
}

$values = explode("#" , $f);

$title = "Money Alert";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";

$boothAlert = $baseController->App_boothAlertDefModel->getAlerts($ID, 12);

if($boothAlert){
    $val = $boothAlert[0]["value"];
    if($val == "none"){
         $content .= "The Money alert is not active.<br/>";
    }
    else{
        $content .= "When the cash box reached $val $/€/&pound;/kr... , you will receive an alert. <br/>";
    }
}
else{
        $content .= "The Money alert is not active.<br/>";
}
    $content .= "Select the value for when you would like to receive an alert.";
    
    $content .= "</div>";
    
    
    $content .= "<input type='number' step='100'onchange='toCheckInput();' id='moneyVal' class='popupInputLarge' min='0' value='$val'>";
    
//    $x=0;
//    $content .= "<select class='popupInputLarge' onchange='toCheckInput();' id='moneyVal'>";
//    while($x < sizeof($values)){
//        $v = $values[$x];
//        $v2 = $values[$x+1];
//        if($v== $val){
//            $content .= "<option value='$v' selected>$v2</option>";
//        }
//        else{
//            $content .= "<option value='$v'>$v2</option>";
//        }
//          $x= $x+2;
//    }
    
    $content .= "</select>";
    
    if($val == "0"){
        $buttons .= "<input type='button' class='popup-confirm' value='Turn ON'  onclick='setMoneyAlert($ID); hidePopupv2();'>";
    }
    else {
        $buttons .= "<input type='button' class='popup-confirm' value='Save' id='buttonCancel' onclick='setMoneyAlert($ID); hidePopupv2();'>";
    }
    
    //$buttons .= "<button type='button' class='popup-confirm' onClick='setMoneyAlert($ID); hidePopupv2();'>Accept</buttons>";
    $buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</buttons>";

$content .= <<<HTML
<script>
    function toCheckInput(){
        if($("#moneyVal").val() != "0"){
            
            $("#buttonCancel").css("background-color","#3085d6");
            $("#buttonCancel").val("Save");
        }
        else {
            $("#buttonCancel").css("background-color","#dd6b55");
            $("#buttonCancel").val("Turn OFF");
        }
    }
        
    function setMoneyAlert(id){
        var value =  $("#moneyVal").val();
        if(value == 0){
            value = "none";
        }
        var ajaxData = {value : value, id: id};
        $.ajax({
            url: 'edit/functions/photobooths/setMoneyAlert.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("photobooths", "alerts", id);
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