<?php

 $folderName1 = $URL . "printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
  /*TREURE CARPETA*/
if (!file_exists(G_PATH . "printPhoto/e$ID/PhotoIdUpload/Logo.jpg")) {
    $CLD_CON->OpenRs("SELECT creation_date , id FROM usbs WHERE event_id=$ID");
    while ($CLD_CON->FetchArray()) {
        $creation_date = $CLD_CON->GetArrayField("creation_date");
        $i = $CLD_CON->GetArrayField("id");
        $folderName2 = G_PATH . "usbs/" . $creation_date . $i . "/PhotoIdUpload/Logo.jpg";
        $file_headers2 = @get_headers($folderName2);
        if (!file_exists(G_PATH . "usbs/$creation_date$i/PhotoIdUpload/Logo.jpg")) {
            $exists = false;
        } else {
            $exists = true;
            /*TREURE CARPETA*/
            if(!file_exists(G_PATH . "printPhoto/e$ID/PhotoIdUpload/")){
            mkdir( G_PATH . "printPhoto/e$ID/PhotoIdUpload/" , 0777, true );
            }
            /*TREURE CARPETA*/
            if(copy(G_PATH . "usbs/" . $creation_date . $i . "/PhotoIdUpload/Logo.jpg" , G_PATH . "printPhoto/e$ID/PhotoIdUpload/Logo.jpg")){
            $logo = "printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
            }
            else{
               $logo = "usbs/" . $creation_date . $i . "/PhotoIdUpload/Logo.jpg"; 
            }
        }
    }
} else {
    $exists = true;
    $logo = "printPhoto/e$ID/PhotoIdUpload/Logo.jpg";
}

$title = "Logo";
$content = "";
$buttons = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
////#resize escrit, no funcio
$content .= "Click upload your file, select your logo, Only 1 image to upload. <br/>";
$content .= "<div class='Expression'>File type: JPG, File size: 1024x768</div>";
$content .= "<div class='Britta'>Resolucion: 300dpi.</div>";
$content .= "</div>";
if ($exists) {
    $content .= "<div class='popup-row'>";
    
    $content .= "<div class='popup-col'>";
        $content .= "<div class='popup-row'>";
            $content .= "<p>Actual Logo</p>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<img src='$logo' style='height:200px;'>";
        $content .= "</div>";
    $content .= "</div>";

    $content .= "<div class='popup-col'>";
        $content .= "<form id='logoForm' action='edit/functions/events/uploadLogo.php' enctype='multipart/form-data' class='upload'>";
        $content .= "<input type='file' name='imgFile' id='imgFile' class='popup-input-large' accept='image/jpeg'>";
        $content .= "<input type='hidden' value='$ID' name='id'>";
        $content .= "</form>";
    $content .= "</div>";

    $content .= "<div class='popup-col'>";
        $content .= "<div class='popup-row'>";
            $content .= "<p>New Logo</p>";
        $content .= "</div>";
        $content .= "<div class='popup-row'>";
            $content .= "<div class='preview'></div>";
        $content .= "</div>";
    $content .= "</div>";
    $content .= "<input type='hidden' id='urlBN' value=''>";
    
    $buttons .= "<input type='button' class='popup-confirm' value='Save'  onclick='saveLogo($ID); hidePopupv2();'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
} 
else {
    $content .= "<div class='popup-row'>";
        $content .= "<div class='popup-col'>";
            $content .= "<form id='logoForm' action='edit/functions/events/uploadLogo.php' enctype='multipart/form-data' class='upload'>";
                $content .= "<input type='file' name='imgFile' id='imgFile' class='popup-input-large'>";
                $content .= "<input type='hidden' value='$ID' name='id'>";
            $content .= "</form>";
        $content .= "</div>";
        $content .= "<div class='popup-col popup-margin-right'>";
            $content .= "<div class='preview'></div>";
        $content .= "</div>";
    $content .= "</div>";
    
    $buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveLogo($ID);'>";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";
}

$content .= <<<HTML
<script>
 $(document).ready(function() {
        $("#imgFile").on("change", function() {
            if ($("#imgFile").val() === "") {
            } 
            else {
                $("#logoForm").ajaxForm({
                    beforeSend: function() {

                    },
                    success: function(e) {
                        //alert(e);
                        if(e === "ERROR") {
                            alert("Error");
                        } else {
                            $(".preview").html("<img src='printPhoto/tmp/" + e + "' style='height:200px; width:auto;'>");
                            $(".preview").show(500);                            
                        }
                    },
                    error: function(e) {

                    }
                });
                $("#logoForm").submit();
            }
        });
    });
    
    function saveLogo(id){
         var ajaxData = {id: id};
        $.ajax({
            url: 'edit/functions/events/saveLogo.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  closePopup();
                  hidePopupv2();
                  profile("events" , "printPhoto" , id);
              }
              else {
                  //alert(data);
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