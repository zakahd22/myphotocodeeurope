<?php
require_once "common/global.php";
require_once G_PATH . "common/Classes/StatisticsController.php";
include G_PATH . "common/conexio.php";

$document_root = "/homepages/46/d399659235/htdocs";
$ip = false;
//Funció escriu al log
function writeOnLog($log, $text) {
    $d = date("Y-m-d H:i:s");
    $f = fopen($log, "a+");
    fwrite($f, $d . " - " . $text . "\n");
    fclose($f);
}

function url_get_contents($Url) {
    if (!function_exists('curl_init')) { 
    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
$pass = $_POST['pswd'];
$login= $_POST['username'];
        
if(empty($pass) OR empty($login) OR strstr($login , ' ') OR strstr($pass , ' ')){
    echo "INCORRECT LOGIN";
}else{
    $CLD_CON->OpenRs("SELECT * FROM CLD_Login WHERE username='$login' AND password='$pass'");
    $CLD_CON2 = clone($CLD_CON);

    if($CLD_CON->FetchArray()){
        $typeUser= $CLD_CON->GetArrayField('userType');
        $userId =  $CLD_CON->GetArrayField('id_user');
        $BanUser =  $CLD_CON->GetArrayField('banned');
        if($BanUser == 1){
            echo "ERROR 666 <br/> Please contact your Sales Representative";
        }else{
            $_SESSION['USERNAME'] = $login;
            $_SESSION['USERTYPE'] = $typeUser;
            $_SESSION['USERID'] = $userId;
//            $_SESSION['$anUser'] = $BanUser;
            ob_start();
            //include 'login_loc.php';

            $std = new StatisticsController();
            $ip = $std->getIpUser();
            $std->saveStdOwnerLogin($userId, $typeUser, $ip);

            ob_end_clean();
            if(!isset($_GET['exterior'])){
                echo "main.php";
            }
        }
        
    }else{
       echo "INCORRECT LOGIN";
    }
}
?>
