<?php
$CLD_CON2= clone($CLD_CON);
$idInc = 0;
$CLD_CON->OpenRs("SELECT serialnumber , CLD_Status FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $status = $CLD_CON->GetArrayField("CLD_Status");
}
$CLD_CON->OpenRs("SELECT comment FROM CLD_historyBooth WHERE idBooth=$ID AND (comment LIKE '%Damage%' OR comment LIKE '%Returned%') ORDER BY data DESC LIMIT 1");
if($CLD_CON->FetchArray()){
    $coment = $CLD_CON->GetArrayField("comment");
    $pos1 = strpos($coment, '#', 0) +1; 
    $lgh = strpos($coment, '#', $pos1) -$pos1;
    $idInc = substr($coment, $pos1 , $lgh);
    $CLD_CON2->OpenRs("SELECT * FROM CLD_Incidents WHERE id=$idInc");
    if($CLD_CON2->FetchArray()){
        $coment = stripslashes($CLD_CON2->GetArrayField("coment"));
        $c =  "This PhotoBooth is Damaged because: <br/>";
        $c .="<span>$coment</span><br/>";
    }
}

$title = "Photobooth $sn to Refurbished";
$content = "";
$buttons = "";


$content .= "<input type='hidden' id='incident' value ='$idInc'> ";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "$c";
    $content .= "How has been solved?<br/>";
$content .= "</div>";

$content .= "<textarea style='width:300px;height:150px;' id='coment' class='areaText' maxlength='200'></textarea>";
$content .= "<p style='width:80%;margin-left:10%;text-align:right;margin-top:0px;margin-bottom:0px; ' id='lng'>0/200</p>";

$buttons .= "<button type='button' class='popup-confirm' onclick='toRefurbished($ID , $status , $idInc); hidePopupv2();'>Accept</button>";
$buttons .= "<button type='button' class='popup-cancel' onclick='hidePopupv2();'>Cancel</button>";

$content .= <<<HTML
<script>
    function toRefurbished(id , from , idInc){
        var coment = $("#coment").val();     
        if(coment.length > 0 ){
           var ajaxData = {id: id , coment : coment ,from: from , inc:idInc };
                $.ajax({
                    url: 'edit/functions/photobooths/toRefurbished.php',
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