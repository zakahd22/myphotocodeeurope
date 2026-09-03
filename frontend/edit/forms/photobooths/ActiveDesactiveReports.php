<?php


$baseController->createModel('App_boothConfigDef');

$pbConfig = $baseController->App_boothConfigDefModel->getApp_boothConfigDef($ID, 1);
if($pbConfig){
    $value = $pbConfig[0]["value"];
} else{
    $value = "NO";
}

$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "This option will keep you informed regarding the PhotoBooth.<br/ >";
$content .= "By activating this option, you will receive weekly, monthly and yearly emails along with a report of the PhotoBooth's activity (sales, cash, stock, etc...).<br /><br/>";
$content .= "You will receive this email to the email address that you have provided in your profile under Email Alerts.<br/><br/>";
 if($value==="NO"){
    $title = "Reports";
//    $content .= "<div class='popup-row popup-margin-top'>The email reports option is not active for this PhotoBooth.</div>"; 
    $buttons .= "<input type='button' class='popup-confirm' style='height:auto;' value='Turn ON' onclick='setStatReport(\"YES\" , $ID); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' style='height:auto;' value='Cancel'  onclick='hidePopupv2();'>";
 }
 if($value==="YES"){
    $content .= "The email reports options is active for this Photobooth.<br />";
    
    $title = "Reports";
//    $content .= "<div class='popup-row popup-margin-top'>The email reports option is active for this PhotoBooth.</div>"; 
    $buttons .= "<input type='button' class='popup-confirm' style='height:auto; background-color:#dd6b55;' value='Turn OFF' onclick='setStatReport(\"NO\" , $ID); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' style='height:auto;' value='Cancel' onclick='hidePopupv2();'>";
    
 }

$content .= "</div>";
 
$content .= <<<HTML
<script>
    function setStatReport(value , id){
        var ajaxData = {value : value, id: id};
        $.ajax({
            url: 'edit/functions/photobooths/setStatReport.php',
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