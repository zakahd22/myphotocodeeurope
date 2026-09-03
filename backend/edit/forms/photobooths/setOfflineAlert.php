<?php

$baseController->createModel('App_booths');


$title = "Offline Alert ";
$content = "";
$buttons = "";

$pbs = $baseController->App_boothsModel->getBoothWhereid($ID);

if($pbs){
    $val = $pbs[0]["alertOffline"];
    if ($val == 0) {
        $content .= "<div class='popup-text popup-margin-bottom'>";
            $content .= "The offline alert is not active.<br/>";
        $content .= "</div>";
        $hs = 0;
        $ms = 0;
        $he = 0;
        $me = 0;
        $timeZone = "";
    } 
    else {
        $hs = $pbs[0]["hS"];
        $ms = $pbs[0]["mS"];
        $he = $pbs[0]["hE"];
        $me = $pbs[0]["mE"];
        $timeZone = $pbs[0]["timeZone"];
        
        if($hs<10){
            $h1 = "0".$hs;
        }
        else{
            $h1 = $hs;
        }
        if($he<10){
            $h2 = "0".$he;
        }
        else{
            $h2 = $he;
        }
        if($ms<10){
            $m1 = "0".$ms;
        }
        else{
            $m1 = $ms;
        }
        if($me<10){
            $m2 = "0".$me;
        }
        else{
            $m2 = $me;
        }
        
        $content .= "<div class='popup-text popup-margin-bottom'>";
            $content .= "You will receive an email if the PhotoBooth will be offline between  $h1:$m1 to $h2:$m2 ($timeZone)";
        $content .= "</div>";
    }
}

else{
    $title = "Offline Alert ";
    $content .= "";
} 

$content .= "<div class='popup-row popup-margin-top' style='width:300px;'>";
    $content .= "<div class='popup-margin-right'>";
        $content .= "Start Time:";
    $content .= "</div>";
    $content .= "<div >";
        $content .= "<select class='popupInputLarge' id='HS'>";
        $x = 0;
        while ($x < 24) {
            if ($x == $hs) {
                $content .= "<option value='$x' selected>";
            } 
            else {
                $content .= "<option value='$x'>";
            }

            if ($x < 10) {
                $content .= "0$x";
            } 
            else {
                $content .= $x;
            }
            $content .= "</option>";
            $x++;
        }
        $content .= "</select>:";
        $content .= "<select class='popupInputLarge' id='MS'>";
        $x = 0;
        while ($x < 60) {
            if ($x == $ms) {
                $content .= "<option value='$x' selected>";
            } 
            else {
                $content .= "<option value='$x'>";
            }

            if ($x < 10) {
                $content .= "0$x";
            } 
            else {
                $content .= $x;
            }
            $content .= "</option>";
            $x++;
        }
        $content .= "</select>";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row popup-margin-top' style='width:300px;'>";
    $content .= "<div class='popup-margin-right'>";
        $content .= "End Time:";
    $content .= "</div>";
    $content .= "<div>";
        $content .= "<select class='popupInputLarge' id='HE'>";
        $x = 0;
        while ($x < 24) {
            if ($x == $he) {
                $content .= "<option value='$x' selected>";
            } 
            else {
                $content .= "<option value='$x'>";
            }

            if ($x < 10) {
                $content .= "0$x";
            } 
            else {
                $content .= $x;
            }
            $content .= "</option>";
            $x++;
        }
        $content .= "</select>:";
        $content .= "<select class='popupInputLarge' id='ME'>";
        $x = 0;
        //20150704  while ($x < 24) {
        while ($x < 60) {//20150704

            if ($x == $me) {
                $content .= "<option value='$x' selected>";
            } 
            else {
                $content .= "<option value='$x'>";
            }

            if ($x < 10) {
                $content .= "0$x";
            } 
            else {
                $content .= $x;
            }
            $content .= "</option>";
            $x++;
        }
        $content .= "</select>";
    $content .= "</div>";
$content .= "</div>";

$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

$content .= "<div class='popup-row popup-margin-top'>";
    $content .= "<div class='popup-col'>";
        $content .= "Time Zone:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<select class='popupInputLarge' id='timeZone'>";
        $x = 0;
        while ($x < sizeof($tzlist)) {
            if ($tzlist[$x] == $timeZone) {
                $content .= "<option value='$tzlist[$x]' selected>";
            } 
            else {
                $content .= "<option value='$tzlist[$x]'>";
            }
            $content .= "$tzlist[$x]</option>";

            $x++;
        }
        $content .= "</select>";
    $content .= "</div>";
$content .= "</div>";


if($val==0){
    $buttons .= "<button type='button' class='popup-confirm' onclick='setOfflineAlert($ID , 1); hidePopupv2();'>Turn ON</button>";
    $buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";
}
else{
    $buttons .= "<button type='button' class='popup-confirm' onclick='setOfflineAlert($ID , 0); hidePopupv2();' style='margin-right:20px; background-color:#dd6b55;'>Turn OFF</button>";
    $buttons .= "<button type='button' class='popup-confirm' onclick='setOfflineAlert($ID , 1); hidePopupv2();'>Save</button>";
    $buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";
}

$content .= <<<HTML
<script>
    function setOfflineAlert(id , active){
        var time =  $("#timeZone").val();
        var hs = $("#HS").val();
        var ms = $("#MS").val();
        var he = $("#HE").val();
        var me = $("#ME").val();
    
       var ajaxData = {id: id , hs : hs , he:he , ms:ms , me : me , timeZone : time , active : active};
        $.ajax({
            url: 'edit/functions/photobooths/setOfflineAlert.php',
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