<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('CLD_questions_emails');
$baseController->createModel('events');

$events = $baseController->eventsModel->getEventsOwner($owner);

$i = 0;
foreach ($events as $event){ 
    $events_id[$i] = $event["id"];
    $i++;
}

$request = $baseController->CLD_questions_emailsModel->getQuestionsEmails($events_id);

$QuestionsEmails = $request['CLD_questions_emails'];
$events = $request['events'];

$html = "<div class='inContent'>";

$count = 0;
foreach ($QuestionsEmails as $QuestionsEmail){ $count++;}
if($count == 0){ $html .= "<p>You do not have any emails</p>";}
else{ $html .= "<p>You  have ". $count ." emails</p>"; }

$i = 0;
foreach ($QuestionsEmails as $QuestionsEmail){ 
    $event = $QuestionsEmail['event'];
    $emailSend = $QuestionsEmail['email'];
    $eventTitle = $events[$i]['title'];
    
    $html .= <<<HTML
    <ul class='regEmailUL'>
        <li style='width:32%' title='Captured Email' class='link'>
            $emailSend
        </li>
        <li style='width:32%;' title='Event' class='link' onclick='openLink("Events", "$event");'>
            $eventTitle
        </li>
    </ul>
HTML;
    $i ++;
}
$html .= "</div>";

echo $html;


