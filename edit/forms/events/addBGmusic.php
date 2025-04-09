<?php
$title = "Background Music";

$content = "";
$buttons = "";

//$content .= "<p> MP3 5MB</p>";
$CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $ID");
$CLD_CON->FetchArray();
$boothType = $CLD_CON->GetArrayField('boothtype_char');
$creationDate = $CLD_CON->GetArrayField("creation_date");
$eventID = $CLD_CON->GetArrayField("event_id"); 

$exists = false;

if(file_exists(G_PATH . "usbs/$creationDate$ID/PhotoIdUpload/BGmusic.mp3")){
    $music = "<audio src='".G_PATH."usbs/$creationDate$ID/PhotoIdUpload/BGmusic.mp3' controls ></audio>";
    $exists = true;
}

$content .= "<div class='popup-text'>";
$content .= "Select your file.<br/>File type: MP3, maximum size: 5Mb";
$content .= "</div>";

if($exists) {
    $content .= <<<HTML
    <div>
        <p style='margin-bottom:20px;'>Actual Background Music</p>
        <div style='width:90%;margin-left:5%;'>
            {$music}
        </div>
    </div>

    <div  style='display:inline;float:left;background-color:yellowgreen;color:white;text-align:center;height:48%;'>
        <img src='images/web/flecha.png' style='width: 60%;height: 75px'>
        <div class='upload-dos'>
            <div class='upload'>
                <form id='musicForm' action='edit/functions/events/uploadMusic.php' enctype='multipart/form-data'>
                <input type='file' name='musicFile' id='musicFile' accept='audio/mp3' class='popup-input-large'>
                <input type='hidden' value='{$ID}' name='id'>
                </form>
            </div>
        </div>
    </div>

    <div id='newBg' style='display:inline;float:left;height:47%;text-align:center;'>
        <p style='margin-bottom:20px;'>New Background Music :</p>
        <div class='preview' style='display:none;width:90%;margin-left:5%;'></div>
    </div>
    <input type='hidden' id='urlBN' value=''>
HTML;
} 
else {
    $content .= "
        <div>
            <div class='upload-dos'>
                <div class='upload'>
                    <form id='musicForm' action='edit/functions/events/uploadMusic.php' enctype='multipart/form-data'>
                        <input type='file' name='musicFile' id='musicFile' class='popup-input-large'>
                        <input type='hidden' value='{$ID}' name='id'>
                    </form>
                </div>
            </div>
            <div class='preview' ></div>
        </div>
    ";
}

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveMusic($ID , $creationDate); hidePopupv2();'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= '
<script>
 $(document).ready(function() {
        $("#musicFile").on("change", function() {
          
        
            if ($("#musicFile").val() === "") {
                  
            
            } else {
                $("#musicForm").ajaxForm({
                    beforeSend: function() {

                    },
                    success: function(e) {
                        //alert(e);
                        if(e === "ERROR") {
                            alert("Error");
                        } 
                        else {
                            $(".preview").html("<audio src=\'printPhoto/tmp/"+e+"\' controls ></audio>");
                        }
                    },
                    error: function(e) {

                    }
                });
                $("#musicForm").submit();

            }
        });
    });
    
    function saveMusic(id , d){
     var bbb = $("#SELECTEDBOOTH").val();    
         var ajaxData = {id: id , cDate : d};
        $.ajax({
            url: \'edit/functions/events/saveMusic.php\',
            type: \'POST\',
            //Ajax events
            success: function(data) {
                //alert(data);
                if(data==="OK"){
                    closePopup();
                    profile("events" , "photobooths" , '.$eventID.');
                    var f = d + "" + id;
                    var a = \''.$boothType.'\';
                    setTimeout(function(){
                    $("#setString" + id).val("4");
                    canviaApartat2(id , a , f , 4 , bbb);
                    } , 1500);
                }
            },
            // Form data
            data: ajaxData,
            contentType: \'application/x-www-form-urlencoded\'
        });
    }
</script>
';

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);