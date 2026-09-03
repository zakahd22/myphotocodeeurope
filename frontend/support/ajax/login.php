<?php
require_once '../../common/global.php';
require_once G_PATH . 'common/conexio.php';

$pass = $_POST['pswd'];
$login= $_POST['username'];

if(empty($pass) OR empty($login) OR strstr($login , ' ') OR strstr($pass , ' ')){
    echo "index.php?error=1";
}
else{
    $CLD_CON->OpenRs("SELECT * FROM CLD_Login WHERE username='$login' AND password='$pass'");
    if($CLD_CON->FetchArray()){
        $typeUser= $CLD_CON->GetArrayField('userType');
        $userId =  $CLD_CON->GetArrayField('id_user');
        $_SESSION['USERNAME'] = $login;
        $_SESSION['USERTYPE'] = $typeUser;
        $_SESSION['USERID'] = $userId;
        if(!isset($_GET['exterior'])){
        echo "main.php";
    }
    /*else{
        header("Location: ../../../../main.php");
    }*/
    }
    else{
        echo "INCORRECT LOGIN"; 
    }
}
?>
