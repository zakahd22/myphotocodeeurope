<?php

$baseController->createModel('events');
$baseController->createModel('event_backgrounds');

$events = $baseController->eventsModel->getEvent($ID);

if($events){
    $rnd = rand(200000, 9999999);
    $bgId = $events[0]['background_id'];
    $dataEvent = $events[0]['start_date'];
}

$title = "Background";
$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
    $content .= "Select the new background";
$content .= "</div>";

$content .= "<div style='margin-top:5px;' class='popup-row popup-center'>";

$content .= "<div class='popup-col popup-center'>";
$content .= "Current Background<br/>";
if ($bgId == 99) {
    $content .= "<img style='width:200px;' src='events/" . $dataEvent . $ID . "/background.jpg?version=$rnd' >";
}
if ($bgId < 99 && $bgId > 0) {
    
    $event_backgrounds = $baseController->event_backgroundsModel->getBackground($bgId);
    
    $CLD_CON->OpenRs("SELECT * FROM event_backgrounds WHERE id=$bgId");
    $CLD_CON->FetchArray();

    $color = $CLD_CON->GetArrayField('color');
    $imgBg = $CLD_CON->GetArrayField('image_url');
    $align_x = $CLD_CON->GetArrayField('align_x');
    $align_y = $CLD_CON->GetArrayField('align_y');
    $repeat = $CLD_CON->GetArrayField('repeat');
   
    $style = "background:";
    if (!empty($color))
         $style .=  "#" . $color;
    if (!empty($imgBg))
        $style .= " url('assets/images/backgrounds/" . $imgBg . "'?version=$rnd)";
    $style .= " " . $align_x;
    $style .= " " . $align_y;
    $style .= " " . $repeat;
    $style .= ";";
    $style .= "border: 1px solid black;";
    $content .= "<div style='$style'></div>";
}
if ($bgId == 0) {
    $content .= "<img src='assets/images/backgrounds/background-default.jpg?version=$rnd' style='height:200px;'>";
}
$content .= "</div>";

$content .= "<div class='popup-col popup-center' style='width:35%;'>";

$content .= "<div class='popup-row popup-center'>";
$content .= "Predefined Background <br/>";
$content .= "</div>";

$content .= "<div class='popup-row popup-center'>";
$content .= "<select name='default' onchange='defaultBG($ID);' id='dftBG' style='margin-bottom:5px'>";
    $content .= "<option value='0'>None</option>";

    $CLD_CON->OpenRs("SELECT id , title FROM event_backgrounds WHERE rental_id=0");
while ($CLD_CON->FetchArray()) {

    $background_name = $CLD_CON->GetArrayField("title");
    $background_id = $CLD_CON->GetArrayField("id");
    $content .= "<option value='$background_id'";
    if ($background_id == $bgId) {
        $content .= " selected ";
    }
    $content .= ">$background_name</option>";
}
$content .= "</select>";
$content .= "</div>";

$content .= "<div class='popup-row popup-center'>";
$content .= "Custom Background<br/>";
$content .= "</div>";

$content .= "<div class='popup-row popup-center'>";
$content .= "<div class='upload'>";
$content .= <<<HTML
<form id='bgForm' action='edit/functions/events/uploadBGimg.php' enctype='multipart/form-data'>
    <input type='file' name='imgFile' id='imgFile' class='popup-input-large' accept='image/jpeg'>
    <input type='hidden' value='$ID' name='id'>
</form>
HTML;
$content .= "</div>";
$content .= "</div>";

$content .= "</div>";

$content .= "<div class='popup-col popup-center'>";
$content .= "New Background:<br />";
$content .= "<div class='preview' style='border:1px solid black; width:243px; height:200px;'></div>";
$content .="</div>";

$content .="</div>";

//$content .="</div>";

$content .= "<input type='hidden' id='dc' value=''>";

$buttons .= "<input type='button' class='popup-confirm' value='Save'  onclick='saveBackground($ID);'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    $(document).ready(function() {
        $("#imgFile").on("change", function() {
            if ($("#imgFile").val() === "") {
            } else {
                $("#bgForm").ajaxForm({
                    beforeSend: function() {

                    },
                    success: function(e) {
                        if(e === "ERROR") {
                            swal("Error", e, "error");
                        } else {
                            var x = Math.floor((Math.random() * 1000000) + 1);
                            $("#dc").val("1");
                            $(".preview").html("<img src='images/ownerIMG/tmp/" + e +"?version="+ x +"' height='243px' width='200px'>");
                            $(".preview").show(500);                            
                        }
                    },
                    error: function(e) {

                    }
                });
                $("#bgForm").submit();

            }
        });
    });
    
    function saveBackground(id){
    var dc = $("#dc").val();
    var bgSel = $("#dftBG").val();
    if(dc===""){
        closePopup();
        profile("events" , "cloud" , id);
    }else{
    var ajaxData = {id: id , dc : dc , bg:bgSel};
        $.ajax({
            url: 'edit/functions/events/saveCustomBg.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  hidePopupv2();
                  closePopup();
                  profile("events" , "cloud" , id);
              }
              else{
                  swal("Error", data, "error");
              }
            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
    }
    
    function defaultBG(id){
        var bgSel = $("#dftBG").val();
        var ajaxData = {id: id , bg:bgSel};
        $.ajax({
            url: 'edit/functions/events/defaultBg.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                  $(".preview").html("");
                  $(".preview").attr("style" , data);
                  $("#dc").val("2");
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