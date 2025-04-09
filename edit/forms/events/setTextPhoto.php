<?php
$c1 = "printPhoto/e$ID/PhotoIdUpload/text.txt";
$c2 = "";
$CLD_CON->OpenRs("SELECT creation_date ,id FROM usbs WHERE event_id=$ID");
while ($CLD_CON->FetchArray()) {
    $creation_date = $CLD_CON->GetArrayField("creation_date");
    $i = $CLD_CON->GetArrayField("id");
    $c2 = "usbs/" . $creation_date . $i . "/";
}
/* TREURE CARPETA */
$folderName1 = G_PATH . $c1;
$folderName2 = G_PATH . $c2 . "PhotoIdUpload/text.txt";

if (file_exists($folderName1)) {
    $text = file_get_contents($folderName1);
} else {
    if (file_exists($folderName2)) {
        move_uploaded_file($folderName2, $folderName1);
        $text = file_get_contents($folderName1);
    } else {
        $text = "";
    }
}

$title = "Text";
$content = "";
$buttons = "";

$content .= "<div class='popup-text'>";
$content .= "Type the text you want to be printed on each photo in the text box below.";
$content .= "</div>";

$content .= "<input type='text' class='popupInputLarge' id='ltxt' value='$text'>";
$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='setText($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .=<<<HTML
<script>
    function setText(id) {
        var txt = $("#ltxt").val();
        var ajaxData = {id: id, t: txt};
        $.ajax({
            url: 'edit/functions/events/setText.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("events", "printPhoto", id);
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