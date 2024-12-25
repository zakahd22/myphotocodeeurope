<?php
$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber , CLD_Status FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");

    $status = $CLD_CON->GetArrayField("CLD_Status");
}

$title = "Photobooth $sn to Returned";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Explain because the photobooth is returned.";
$content .= "</div>";

$content .= "<textarea style='width:80%;margin-left:10%;height:170px;' id='coment' class='popupInputLarge' maxlength='200'></textarea>";
$content .= "<p style='width:80%;margin-left:10%;text-align:right;margin-top:0px;margin-bottom:0px; ' id='lng'>0/200</p>";

$content .= "<div class='popup-row' style='width:300px;'>";
    $content .= "<div class='popup-col popup-center' style='width:300px;'>";
        $content .= "<p><input type='checkbox' class='popupInputLarge' style='height:auto; width:auto; float:left;' id='Damaged'>";
        $content .= "Is Damaged</p>";
    $content .= "</div>";
$content .= "</div>";



$buttons .= "<button type='button' class='popup-confirm' onclick='toDamage($ID , $status); hidePopupv2();'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toDamage(id , from){
        var damage = 0;
        if($("#Damaged").is(":checked")){
            damage = 1;
        }
        var coment = $("#coment").val();
        if(coment.length > 0 ){
           var ajaxData = {id: id , coment : coment ,from: from , damage : damage};
                $.ajax({
                    url: 'edit/functions/photobooths/toReturned.php',
                    type: 'POST',
                     before: function(){loadPopUp();},
                    success: function(data) {
                        unloadingPopUp();
                        if (data === "OK") {                           
                            closePopup();
                            profile("photobooths" , "info", id);
                        } else {
                            swal('error', data);
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });         
        }else{
            alert("Comment is empty and is required");
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