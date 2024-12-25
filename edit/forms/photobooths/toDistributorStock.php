<?php
$CLD_CON->OpenRs("SELECT serialnumber , CLD_Status FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $status = $CLD_CON->GetArrayField("CLD_Status");
}

$title = "Change PhotoBooth $sn to Stock";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "TEXT EXPLICATIU";
$content .= "</div>";

//$buttons .= "<button type='button' class='popup-confirm' onclick='toDistributor($ID ,$status); hidePopupv2();'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toDistributor(id ,status){
        var dis = $("#distributor").val();
        if(dis === "0"){
            alert("Please select a distributor");
        }else{
            var ajaxData = {dis: dis , id : id , from : status};
              $.ajax({
                    url: 'edit/functions/photobooths/toDistributor.php',
                    type: 'POST',
                     before: function(){loadPopUp();},
                    success: function(data) {
                         unloadingPopUp();
                        if (data === "OK") {
                            closePopup();
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

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);