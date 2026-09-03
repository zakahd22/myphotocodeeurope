<?php
include '../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

$event = $_GET['event'];
$CLD_CON2= clone($CLD_CON);
$CLD_CON2->OpenRs("SELECT * FROM CLD_questions WHERE event=$event");
$i=0;
$errors= "";
$execute = array();


utils::log($_GET, "logQuestions");

while ($CLD_CON2->FetchArray()) {
    $quest_num = $CLD_CON2->GetArrayField("question_number");
    if ($quest_num == 1) {
        $email = $_GET['email'];
        if (!ereg("^([a-zA-Z0-9._]+)@([a-zA-Z0-9.-]+).([a-zA-Z]{2,4})$", $email)) {
            $errors = "The email is not correct ";
        }
        else{
            $execute[$i] = "INSERT INTO CLD_questions_emails (event , email) VALUES($event , '$email')";
        }
    }
    else{
        $question_name = "q$quest_num";
        if(isset($_GET[$question_name])){
            $ask = $_GET[$question_name];
            if($ask==1){
                $execute[$i] = "UPDATE CLD_questions e SET e.r1 = e.r1 + 1 WHERE e.event =$event AND e.question_number=$quest_num";
            }
            else{
                $execute[$i] = "UPDATE CLD_questions e SET e.r2 = e.r2+1 WHERE e.event =$event AND e.question_number=$quest_num";
            }
        }
        else{
            $errors = "Ask all questions please";
        }
    }
    $i++;
}

if(empty($errors)){
    $_SESSION["{$event}_aproved"] = 1;
    
    $i=0;
    while($i< sizeof($execute)){
        $ex = $execute[$i];
        $CLD_CON->Execute($ex);
        $i++;
    }
    echo "OK";
}
else{
    echo "$errors";
}

