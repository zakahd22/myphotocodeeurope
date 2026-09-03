<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('registre_emails');
$baseController->createModel('photos');
$baseController->createModel('CLD_questions_emails');

$ID = $_POST['id'];

$html  = "<div class='inContent'>";
if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
    $html  .= "<div class='boxLeft'>";
    $html  .= "<h1>Shared E-mails <input type='button' class='miniDownload' onclick='downloadEmails($ID , \"downloadEmailEvent.php\" );'></h1>";
    $html  .= "<p>There are E-mails captured when the users send their photos via E-mail.</p>";
    $html  .= "<div class='emailsList'>";
    
    $request = $baseController->registre_emailsModel->getRegistreEmailEvent($ID);
    
    if($request){
        foreach ($request as $mail){
            $html  .= "<p class='emailItem'>";
            $html  .= $mail["email"];
            $html  .= "</p>";
        }
    }
    
    $html  .= "</div>";
    $html  .= "</div>";
}
$html  .= "<div class='boxRight'>";
if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6)$html  .= "<h1>Question E-mails <input type='button' class='miniDownload' onclick='downloadEmails($ID , \"downloadQuestionsEmails.php\" );'></h1>";
else $html  .= "<h1>E-mails <input type='button' class='miniDownload' onclick='downloadEmails($ID , \"downloadQuestionsEmails.php\" );'></h1>";
$html  .= "<p>These are E-mails that your request from your users when they want to view their photos.</p>";
$html  .= "<div class='emailsList' >";

$questionEmails = $baseController->CLD_questions_emailsModel->getQuestionsEmail($ID);
foreach ($questionEmails as $questionEmail){
    $html  .= "<p class='emailItem'>";
        $html  .= $questionEmail["email"];
    $html  .= "</p>";
}
$html  .= "</div>";
$html  .= "</div>";
$html  .= "</div>";

echo $html;
?>

<script>
    function downloadEmails(id, file) {
        loading();
        var ajaxData = {id: id};
        $.ajax({
            url: 'sections/events/functions/' + file,
            type: 'POST',
            success: function(data) {
                if (data === "ERROR") {
                    error(data);
                } else {
                    window.location.assign(data);
                }
                setTimeout(function() {
                    profile("events", "emails", id);
                }, 2000);


            },
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }

</script>