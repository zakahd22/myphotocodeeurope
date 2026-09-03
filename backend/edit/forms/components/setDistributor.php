<?php
$CLD_CON2 = clone($CLD_CON);

$CLD_CON->OpenRs("SELECT distributor FROM CLD_components WHERE serialnumber='$ID'");
if($CLD_CON->FetchArray()){
    $distributor = $CLD_CON->GetArrayField("distributor");
    if(empty($distributor)){
        $text= "Currently there are no distributor";
    }else{
        $CLD_CON2->OpenRs("SELECT Name From CLD_Distributors WHERE id=$distributor");
        if($CLD_CON2->FetchArray()){
            $disName = $CLD_CON2->GetArrayField("Name");
        }
        $text = "Currently on <b>$disName</b>";
    }
}

$title = "Distributor";
$content = "";
$buttons = "";
$content .= "<div class='popup-text'>";
    $content .= "<br/><center>$text</center><br/><br/>";
    $content .= "Select new Distributor :";
$content .= "</div>";

$content .= "<div class='popup-text'>";
    $content .= "<select class='popup-input-large' id='distributor'>";
    $content .= "<option value='0'> ---- Select a Distributor ----</option>";
    $CLD_CON->OpenRs("SELECT id , Name FROM CLD_Distributors");
    while($CLD_CON->FetchArray()){
        $id = $CLD_CON->GetArrayField("id");
        $Name = $CLD_CON->GetArrayField("Name");
        $content .= "<option value='$id'> $Name </option>";
    }
    $content .= "</select>";
$content .= "</div>";

$content .= <<<HTML
<script>
    function toDistributor(id){
        var dis = $("#distributor").val();
        if(dis === "0"){
            alert("Please select a distributor");
        }else{
            var ajaxData = {dis: dis , id : id };
              $.ajax({
                    url: 'edit/functions/components/toDistributor.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {
                            closePopup();
                            profile("components", "Info", id);
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

$buttons .= "<input type='button' class='popup-confirm' value='Save'  onclick='toDistributor(\"$ID\"); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);