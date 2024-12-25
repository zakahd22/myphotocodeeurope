<?php

$idDongle = 0;

$baseController->createModel('App_boothDongle');
$baseController->createModel('booths');

$boothDongle = $baseController->App_boothDongleModel->boothDongleLmit($ID);
//
//if($boothDongle){
//    $idDongle = $boothDongle[]["idDongle"];
//}

$title = "New Dongle Pairing";
$content = "";
$buttons = "";

//$content .= "Crear un nou aparallement nomes en el cas de que la màquina no en tingui cap altre o en el cas que s'hagi canviat de impresora.";
$content .= "<div class='popup-row'>"
        . "<div class='popup-col'>"
            . "Dongle:"
        . "</div><div class=popup-col'>";
                
            $content .= "<select class='popupInputLarge' id='dongle'>";
            if($idDongle == 0){
                $content .= "<option value=0 selected> ---- </option>";
            }
            
//            $booths = $baseController->boothsModel->getBoothsOrder();
            
            $CLD_CON->OpenRs("SELECT id , rand_string FROM booths  ORDER BY rand_string");
            
            while($CLD_CON->FetchArray()){
                $idD = $CLD_CON->GetArrayField("id");
                $str = $CLD_CON->GetArrayField("rand_string");
                $selected= "";
                if($idD == $idDongle){
                    $selected= "selected";
                }
                $content .= "<option value='$idD' $selected>$str</option>";
            }
            $content .= "</select>";


    $content .= "</div>"
    . "</div>";

$content .= "<input type='hidden' id='oldDongle' value='$idDongle' >";

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='newPairing({$ID}); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function newPairing(id){
        var oldDongle = $("#oldDongle").val();
        var newDongle = $("#dongle").val();
        if(oldDongle != newDongle && newDongle !=0){
            var ajaxData = {"id": id, "dongle": newDongle};
            console.log(ajaxData);
            $.ajax({
                url: 'edit/functions/photobooths/newPairing.php',
                type: 'POST',
                success: function(data) {
                    if (data === "OK") {                           
                        closePopup();
                        profile("photobooths" , "pairings", id);
                    } else {
                        alert(data);
                    }
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });        
            
        }else{
            closePopup();
        }
        
    }
</script>
HTML;
$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);