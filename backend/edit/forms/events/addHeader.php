<?php
$title = "HEADER (Wedding version)";
$content = "";
$buttons = "";

$CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $ID");
$CLD_CON->FetchArray();
$boothType = $CLD_CON->GetArrayField('boothtype_char');
$creationDate = $CLD_CON->GetArrayField("creation_date");
$eventID = $CLD_CON->GetArrayField("event_id"); 
$CLD_CON->OpenRs("SELECT b.* FROM booth_types b WHERE b.char='$boothType'");
$CLD_CON->FetchArray();
$welcome_width = $CLD_CON->GetArrayField('welcome_w');
$welcome_height = $CLD_CON->GetArrayField('welcome_h');

$content .= "<div class='popup-text'>";
$content .= "Upload 1 image/s. File type: JPG , File size:". $welcome_width." x ".$welcome_height." (pixels).";
$content .= "</div>";
$exists = false;

if(file_exists(G_PATH . "usbs/$creationDate$ID/PhotoIdEvents/Wedding/Header/1.jpg")){
    $img = "<img src='usbs/$creationDate$ID/PhotoIdEvents/Wedding/Header/1.jpg'></img>";
    $exists = true;
}
if($exists) {
    
    $content .= "<form id='headerForm' action='edit/functions/events/uploadHeader.php' enctype='multipart/form-data'>";
    $content .= "<input class='popup-input-large' type='file' name='headerFile' id='headerFile' accept='image/jpeg'>";
    $content .= "<input type='hidden' value='$ID' name='id'>";
    $content .= "</form>";
        
    $content .= "<div style='max-height:300px'>";    
        $content .= "<div class='popupImageHeader' >";
            $content .= "Actual Header:<br/>";
            $content .= "<div class='preview'>";
            $content .= $img;
            $content .= "</div>";
        $content .= "</div>";

        $content .= "<div class='popupImageHeader' >";
            $content .= "New Header:<br/>";
            $content .= "<div class='preview'></div>";
        $content .= "</div>";        
        
        $content .= "<input type='hidden' id='urlBN' value=''>";
    $content .= "</div>";
    
    $buttons .= "<input type='button' class='popup-confirm' value='Accept' onclick='saveHeader($ID);'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
} else {
    $content .= "<div class='popup-text popup-margin-top popup-margin-bottom'>";
    $content .= "No Header yet , select your file, JPG 800x600";
    $content .= "</div>";
    $content .= "<div style='max-height:300px'>";
    $content .= "<div class='upload'>";
    $content .= "<form id='headerForm' action='edit/functions/events/uploadHeader.php' enctype='multipart/form-data'>";
    $content .= "<input class='popup-input-large' type='file' name='headerFile' id='headerFile' accept='image/jpeg'>";
    $content .= "<input type='hidden' value='$ID' name='id'>";
    $content .= "</form></div>";
    $content .= "<div class='preview'></div>";
    $content .="</div>";
    $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveHeader($ID , $creationDate);'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
} 

$content .= <<<HTML
<script>
 $(document).ready(function() {
        $("#headerFile").on("change", function() {
            if ($("#headerFile").val() === "") {
            
            } else {
                $("#headerForm").ajaxForm({
                    beforeSend: function() {

                    },
                    success: function(e) {
                        if(e === "ERROR") {
                            swal("Error", "", "error");
                        } else {
                            $(".preview").html("<img src='printPhoto/tmp/"+e+"' >");
                            $(".preview").show(500);                            
                        }
                    },
                    error: function(e) {

                    }
                });
                $("#headerForm").submit();
            }
        });
    });
    
    function saveHeader(id , d){
         var ajaxData = {id: id , cDate : d};
        $.ajax({
            url: 'edit/functions/events/saveHeader.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  closePopup();
                  hidePopupv2();
                  profile("events" , "photobooths" , $eventID);
                                   
                  var f = d + "" + id;
                        var a = '$boothType';
                        setTimeout(function(){
                            $("#setString" + id).val("5");
                            canviaApartat2(id , a , f , 5 );
                        } , 1500);
              }
              else{
                swal("Error", data, "error");
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
