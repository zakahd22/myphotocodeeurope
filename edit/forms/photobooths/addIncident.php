<?php
$title = "New Incident";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Type a small description about the issue.";
$content .= "</div>";

$content .= "<textarea style='width:80%;margin-left:10%;height:200px' id='coment' class='popupInputLarge' maxlength='200'></textarea>";
$content .= "<p style='width:80%;margin-left:10%;text-align:right;margin-top:0px;margin-bottom:0px; ' id='lng'>0/200</p>";

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='addIncident($ID); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML
<script>
    function addIncident(id){
        var coment = $("#coment").val();
        if(coment.length > 0 ){
           var ajaxData = {id: id , coment : coment };
                $.ajax({
                    url: 'edit/functions/photobooths/newIncident.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {                           
                            closePopup();
                            profile("photobooths" , "incidents", id);
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