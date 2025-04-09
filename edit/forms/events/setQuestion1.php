<?php

$baseController->createModel('CLD_questions');

$questions = $baseController->CLD_questionsModel->getEventsByQuestionNumber($ID);
   
$title = "Ask Email";

$content = "";
$buttons = "";

if($questions){
    $content .= "<div class='popup-text'>";
    $content .= "<b>This option is turned ON.</b>";
    $content .= "</div>";
    $buttons .= "<input type='button' class='popup-confirm' value='Turn OFF' style='background-color:#dd6b55;' onclick='OnOffQuestion($ID , 0); hidePopupv2();' style='background-color:#dd6b55;' >";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
}
else{
    $content .= "<div class='popup-text'>";
    $content .= "<center><b>This option is turned OFF.</b></center><br/>Activate this option if you want your customers email.";
    $content .= "</div>";
    $buttons .= "<input type='button' class='popup-confirm' value='Turn ON' onclick='OnOffQuestion($ID , 1); hidePopupv2();' >";
    $buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
}

$content .= <<<HTML
<script>
    function OnOffQuestion(id , active){
        var ajaxData = {id: id , question:1 , active : active};
        $.ajax({
            url: 'edit/functions/events/setQuestions.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
              if(data==="OK"){
                  closePopup();
                  profile("events" , "cloud" , id);
                  setTimeout(function(){ openQuestions(); }, 1000);
              }else{
                  alert("ERROR");
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