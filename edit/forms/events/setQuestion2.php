<?php
$CLD_CON->OpenRs("SELECT id , question , reply1 , reply2 FROM CLD_questions WHERE question_number=2 AND event=$ID");

if ($CLD_CON->FetchArray()) {
    $question = stripslashes($CLD_CON->GetArrayField('question'));
    $reply1 = stripslashes($CLD_CON->GetArrayField('reply1'));
    $reply2 = stripslashes($CLD_CON->GetArrayField('reply2'));
    $text= "This option is active";
    $disabled = "disabled";
    $btn = "<input type='button' class='popup-confirm' value='Turn OFF' style='background-color:#dd6b55;' onclick='OnOffQuestion2($ID , 0); hidePopupv2();'>";
    $btn .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
}else{
    $text= "This option is inactive. ";   
    $question = "";
    $reply1 = "";
    $reply2 = "";
    $disabled = "";
    $btn = "<input type='button' class='popup-confirm' value='Turn ON' onclick='OnOffQuestion2($ID , 1); hidePopupv2();'>";
    $btn .= "<input type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>";
}
    
$title = "Question 1:";
$content = "";
$buttons = "";    

$content .= "$text <br/>";
$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col' style='width:120px;'>";
        $content .= "Question Text:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='text' id='qTxt' class='popupInputLarge' value='$question' {$disabled} >";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col' style='width:120px;'>";
        $content .= "Answer 1:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='text' id='r1' class='popupInputLarge' value='$reply1' {$disabled} >";
    $content .= "</div>";
$content .= "</div>";

$content .= "<div class='popup-row'>";
    $content .= "<div class='popup-col' style='width:120px;'>";
        $content .= "Answer 2:";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<input type='text' id='r2' class='popupInputLarge' value='$reply2' {$disabled} >";
    $content .= "</div>";
$content .= "</div>";

$buttons .= $btn."";

$content .= <<<HTML
<script>
    function OnOffQuestion2(id , active){
        var q = $("#qTxt").val();
        var rp1 = $("#r1").val();
        var rp2 = $("#r2").val();
        var ajaxData = {id : id , question : 2 , active : active , r1 : rp1 , r2 : rp2 , q : q}; 
        
       if(active === 1){
        if(q.length ===0){
            alert("Question Text is required");
            return;
        }
         if(rp1.length ===0){
            alert("Reply 1 is required");
            return;
        }
         if(rp2.length ===0){
            alert("Reply 2 is required");
            return;
        }
       }
       
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