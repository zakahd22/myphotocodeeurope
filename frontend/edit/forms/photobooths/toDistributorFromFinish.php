<?php
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}
$title = "Assign PhotoBooth $sn to Distributor";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Select the distributor you will be sent the PhotoBooth.<br/>";
$content .= "</div>";

$content .= "<select class='popupInputLarge' id='distributor12345'>";
    $content .= "<option value='0'> ---- Select a Distributor ----</option>";
    $CLD_CON->OpenRs("SELECT id , Name FROM CLD_Distributors");
    while($CLD_CON->FetchArray()){
        $id = $CLD_CON->GetArrayField("id");
        $Name = $CLD_CON->GetArrayField("Name");
        $content .= "<option value='$id'> $Name </option>";
    }
$content .= "</select>";

$buttons .= "<button type='button' class='popup-confirm' onclick='toDistributor($ID); hidePopupv2();'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toDistributor(id){
        var dis = $("#distributor12345").val();
        if(dis === "0"){
            alert("Please select a distributor");
        }else{
            var ajaxData = {dis: dis , id : id , from : 1 , to: 2};
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