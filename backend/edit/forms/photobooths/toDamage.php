<?php

$baseController->createModel('App_booths');

$pbs = $baseController->App_boothsModel->getBoothWhereid($ID);

if($pbs){
    $sn = $pbs[0]["serialnumber"];
    $status = $pbs[0]["CLD_Status"];
}

$title = "Photobooth $sn to Damage";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Explain the photobooth Damage.";
$content .= "</div>";

$content .= "<textarea id='coment' style='width:300px; height: 150px;' class='popupInputLarge' maxlength='200'></textarea>";
$content .= "<p style='width:80%;margin-left:10%;text-align:right;margin-top:0px;margin-bottom:0px; ' id='lng'>0/200</p>";

$buttons .= "<button type='button' class='popup-confirm' onclick='toDamage($ID, $status); hidePopupv2();'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toDamage(id , from){
        var coment = $("#coment").val();
        if(coment.length > 0 ){
           var ajaxData = {id: id , coment : coment ,from: from };
                $.ajax({
                    url: 'edit/functions/photobooths/toDamage.php',
                    type: 'POST',
                    before: function(){loadPopUp();},
                    success: function(data) {
                        unloadingPopUp();
                        if (data === "OK") {                           
                            closePopup();
                            profile("photobooths" , "info", id);
                        } else {
                            alert(data);
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });         
        }else{
            alert("Coment is empty and is required");
        }
    }
    $(document).ready(function(){
       $("#coment").on("keyup" , function(){
          var c = $("#coment").val();
          ;
          $("#lng").html(c.length + "/200");
       });
    });    
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);