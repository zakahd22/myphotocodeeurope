<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('photos');

$ID = $_POST['id'];
$ID1 = $_POST['id1'];
$event = $baseController->eventsModel->getEvent($ID);
$date = $event[0]['start_date'];
$folder = $date . $ID;
$i = 1;
$j = 5;

echo "<form id='ImportForm' action='edit/functions/events/okImportPhotos.php' method='POST' enctype='multipart/form-data'>";
echo "<div style='display: flex; display: -webkit-flex; display: -moz-flex; display: -ms-flex; -webkit-flex-direction: row; -moz-flex-direction: row; -ms-flex-direction: row; flex-direction: row;'>";

$photos = $baseController->photosModel->getPhotos($ID);
foreach($photos as $photo){
    $code = $photo['code'];
    $datePhoto = $photo['Appusr_datetime'];
    $img = "events/$folder/$code.jpg";
    $ruta = G_PATH . "events/$folder/$code.jpg";
    if(file_exists($ruta)){
        list($width, $height) = getimagesize($img);
        if($i == $j){
            echo "</div>";
            echo "<div  style='display: flex; display: -webkit-flex; display: -moz-flex; display: -ms-flex; -webkit-flex-direction: row; -moz-flex-direction: row; -ms-flex-direction: row; flex-direction: row;'>";
            $j += 4;
        }
        echo "<div>";
        if ($height > $width) {
            echo "<div class='contentGlobalPhoto'>";
                echo "<div>";
                    echo "<img src='$img' style='height:20%;'>";
                    echo "<br/>";
                echo "</div>";
                echo "<div>";
                    echo "<p>CODE : $code </p>";
                    echo "<p> DATE : $datePhoto </p>";
                    echo "<p><input type='checkbox' name='$code' value='$code'> IMPORT THIS</p>";
                echo "</div>";
            echo "</div>";

        } 
        else {
            echo "<div class='contentGlobalPhoto'>";
                echo "<div class='conentPhoto'>";
                    echo "<img src='$img' style='width:20%;'>";
                    echo "<br/>";
                echo "</div>";
                echo "<div class='conentPhotoInfo'>";
                    echo "<p>CODE : $code </p>";
                    echo "<p> DATE : $datePhoto </p>";
                    echo "<p><input type='checkbox' name='$code' value='$code'> IMPORT THIS</p>";
                echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        $i++;
    }
}
echo '</div>';

echo "<input type='hidden' name='eventFROM' value='$ID'>";
echo "<input type='hidden' name='eventTO' value='$ID1' id='eTo'>";

echo "</form>";
?>
<script>
    $(document).ready(function(){        
        $("#bImp").on("click" , function(){
            $("#ImportForm").ajaxForm({
                success: function(e) {
                    alert(e);
                    if (e === "OK") {
                        var ev = $("#eTo").val();
                        //closePopup();
                        hidePopupv2();
                        //setTimeout(function(){
                        profile("events" , "Photos" , ev);
                        //} , 1000);
                    } 
                    else{
                       alert(e);
                    }
                }
            });
            $("#ImportForm").submit();
        });
            
    });
 </script>