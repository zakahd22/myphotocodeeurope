<?php

require_once "common/global.php";
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('CLD_Login');
$baseController->createModel('CLD_EventsManegers');

$labels_apartat = "function_register";
include 'labels.php';

$errors = "";
$id = 0;

if (isset($_POST['submit'])) {
    $eventID = $_POST['event'];
    $x = true;
    $registredName = addslashes($_POST['registredName']);
    $registredSurname = addslashes($_POST['registredSurnames']);
    $registredEmail = $_POST['email'];
    $registredPwd1 = $_POST['password1'];
    $registredPwd2 = $_POST['password2'];
    $code = $_POST['code'];

    if (empty($registredName)) {
        $errors .= $REG_ERROR1;
        $x = false;
    }
    if (!filter_var($registredEmail, FILTER_VALIDATE_EMAIL)) {
        $errors .= $REG_ERROR2;
        $x = false;
    }
    if (empty($registredPwd1) || empty($registredPwd2)) {
        $errors .= $REG_ERROR3;
        $x = false;
    }
    if ($registredPwd1 != $registredPwd2) {
        $errors .= $REG_ERROR4;
        $x = false;
    }

    $event = $baseController->eventsModel->getEventsRegister($eventID, $code);
    if (count($event) == 0) {
        $errors .= $REG_ERROR5;
        $x = false;
    }

    if ($x) {
        $CLD_LoginUser = $baseController->CLD_LoginModel->getLoginWhereUsername($registredEmail);
        if (count($CLD_LoginUser) == 0) {
            $baseController->entity->loadEntity('CLD_EventsManegers');
            $baseController->entity->setValue("name", $registredName);
            $baseController->entity->setValue("surname", $registredSurname);
            $baseController->entity->setValue("email", $registredEmail);
            $id = $baseController->CLD_EventsManegersModel->insertCLD_EventsManegers();

            $baseController->entity->loadEntity('CLD_Login');
            $baseController->entity->setValue("username", $registredEmail);
            $baseController->entity->setValue("password", $registredPwd1);
            $baseController->entity->setValue("id_user", $id);
            $baseController->entity->setValue("userType", 5);
            $idLogin = $baseController->CLD_LoginModel->insertCLD_Login();

            $updates = array('CLD_eventManegerId' => $id);
            $baseController->eventsModel->updateEvent($eventID, $updates);
        } else {
            $idUser = $CLD_LoginUser[0]['id_user'];
            $updates = array('password' => $registredPwd1);
            $baseController->CLD_LoginModel->updateLoginWhereUsername($registredEmail, $updates);

            $updates1 = array('CLD_eventManegerId' => $idUser);
            $baseController->eventsModel->updateEvent($eventID, $updates1);

            $id = $idUser;
        }
    }
} else {
    $eventID = $_GET['event'];
    $event = $baseController->eventsModel->getEvent($eventID);
    if ($event) {
        $id = $event[0]["CLD_eventManegerId"];
    }
}
?>

<html>
<head>
    <title><?php echo $REG_TITLE; ?></title>
    <link type="text/css" href="includes/logincss.css" rel="stylesheet">
</head>
<body>
    <div style='width:100%;height:100%;overflow:auto;'>
        <center><img src="images/web/myphotocode.png" style="margin-top:50px;width:458px;"></center>
        <div class='register'>
            <?php if ($id == 0) { ?>
                <p><marquee><?php echo $errors ?></marquee></p>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <p><?php echo $REG_NAME; ?><input type='text' name='registredName'>
                    <?php echo $REG_SURNAMES; ?><input type='text' name='registredSurnames'></p>
                    <hr>
                    <p><?php echo $REG_EMAIL; ?><input type='text' name='email' style='width: 50%;'></p>
                    <hr>
                    <p><?php echo $REG_PASSWORD; ?><input type='password' name='password1'>
                    <?php echo $REG_REPEAT; ?><input type='password' name='password2'></p>
                    <hr>
                    <p><?php echo $REG_SECURITY_CODE; ?><input type='password' name='code' style='width: 50%;'></p>
                    <hr>
                    <input type='hidden' name='event' value='<?php echo $eventID; ?>'>
                    <p><input id='registerButton' type='submit' name='submit' value='<?php echo $REG_BUTTON_REGISTER; ?>'></p>
                </form>
            <?php } else { 
                if (isset($x)) {
                    echo "<p>You are registered. Go to <a href='" . G_PAGE . "'>MYPHOTOCODE</a> and login.</p>";
                } else {
                    echo "<p>$REG_ERROR6</p>";
                }
            } ?>
        </div>
    </div>
</body>
</html>
