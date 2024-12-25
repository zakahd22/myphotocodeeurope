<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('CLD_questions_emails');

$arrayIN = array();
$arrayOWNER = array();

/*$USERTYPE = $_SESSION['USERTYPE'];
$USERID = $_SESSION['USERID'];*/
//$USERTYPE = 4;
$USERID = $_POST['id'];
if (!file_exists("../../../temp/emails/$USERTYPE" . "$USERID")){
 mkdir("../../../temp/emails/$USERTYPE" . "$USERID", 0777);
 }
 $f = "../../../temp/emails/$USERTYPE" . "$USERID/emailsQuestions.xls";
 $link = G_PAGE."temp/emails/$USERTYPE" . "$USERID/emailsQuestions.xls";

$x=0; 

switch ($USERTYPE) {
    case 1:
        $baseController->createModel('rentals');
        $rentals = $baseController->rentalsModel->getAllRentals();
        $i=0;
        foreach($rentals as $rental){
            $arrayOWNER[$i]= $rental["id"];
            $i++;
        }
    break;
    
    case 4:
        $arrayOWNER = array($USERID);
    break;    
}

$events = $baseController->eventsModel->getEventsOwnerIN($arrayOWNER);
$i=0;
foreach($events as $event){
    $arrayIN[$i]= $event["id"];
    $i++;
}


$questionMails = $baseController->CLD_questions_emailsModel->getQuestionsEmailIN($arrayIN);
$fp = fopen($f, "w");
$text="";
foreach($questionMails as $qmail){
    $email = $qmail['email'];
    $text .= $email . "\n";
}
 fwrite($fp, $text . PHP_EOL);
 fclose($fp);

echo $link;
?>