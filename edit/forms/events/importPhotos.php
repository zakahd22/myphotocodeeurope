<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('photos');

$labels_apartat = "popup";
include '../../../labels.php';

$title = "";
$content = "";
$buttons = "";

$ID = $_POST['id'];
$event = $baseController->eventsModel->getEvent($ID);
if ($event) {
    $title = stripslashes($event[0]['title']);
    $date = $event[0]['start_date'];
    $date = date("F d, Y", strtotime($date));
    $ownerID = $event[0]['rental_id'];
    
    $title = "Import Photos";
    $content .= "<link rel='stylesheet' href='sections/events/resources/css/importPhotos.css'>";
    $content .= "into $title - $date ";
    
    $events = $baseController->eventsModel->getEventsImport($ownerID, $ID);
    $content .= "From <select name='event2' id='event2' onchange='loadPhotos($ID)'>";
    $content .= "<option value=0>------</option>";
    foreach ($events as $event) {
        $eventID2 = $event['id'];
        $title2 = stripslashes($event['title']);
        $date2 = $event['start_date'];
        $date2 = date("F d, Y", strtotime($date2));
        $content .= "<option value='$eventID2'>$eventID2 $title2 ($date2)</option>";
    }
    $content .= "</select>";
    $content .= "
        <div id='contentSelectAll'>
                Select All 
                &nbsp
                <input type='checkbox' name='selectAll' val='0' id='inputAll' onclick='selectAll(select);'>
        </div>
    ";
    $content .= "<div id='photosDIV' style='width:80%; height:450px; overflow:scroll; border:1px solid gray; display: flex; display: -webkit-flex; display: -moz-flex; display: -ms-flex;'></div>";
}
$buttons .= "<input type='button' class='popup-confirm' id='bImp' value='Import' />";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel'  onclick='hidePopupv2();'>";

$content .= <<<HTML
<script> 
    function selectAll(){
        var value = $("#inputAll").attr("val");
        if(value == 0){
             $("#ImportForm input").prop("checked", true);    
            $("#inputAll").attr("val","1");
        }
        else{
             $("#ImportForm input").prop("checked", false);
            $("#inputAll").attr("val","0");
        }
        
    }

    function loadPhotos(i) {
        var impID = $("#event2 :selected").val();
        if (impID != 0) {
            $("#importButton").show();
            var ajaxData = {id: impID , id1 : i };
            $.ajax({
                url: 'edit/functions/events/getImportPhotos.php',
                type: 'POST',
                //Ajax events
                beforeSend: function() {
                }
                ,
                success: function(data) {
                    $("#photosDIV").html(data);
                },
                error: function() {

                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        } else {
            $("#importButton").hide();
            $("#photosDIV").html("");
        }
    }
</script>
HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);
