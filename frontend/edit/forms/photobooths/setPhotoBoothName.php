<?php
//20150709pbname, és nou
$CLD_CON->OpenRs("SELECT name FROM App_booths WHERE idBooth = $ID");
$loc = "";
if($CLD_CON->FetchArray()){
    $name = $CLD_CON->GetArrayField("name");
}
$title = "PhotoBooth Name";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "The name identifies the PhotoBooth.<br/>";
    $content .= "If you want to change the PhotoBooth’s name, type the new name in the box below.";
$content .= "</div>";

$content .= "<input type='text' class='popupInputLarge popup-margin-top' value='$name' id='pbName'>";

$buttons .= "<button type='button' class='popup-confirm' onClick='setPhotoBoothName($ID); hidePopupv2();'>Save</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function setPhotoBoothName(id){
        var pbName = $("#pbName").val(); 
        var ajaxData ={pbn : pbName , id : id};
         $.ajax({
            url: 'edit/functions/photobooths/setPhotoBoothName.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK"){
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