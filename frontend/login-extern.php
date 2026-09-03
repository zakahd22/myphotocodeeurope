<?php

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
}

require_once "common/global.php";
require_once G_PATH . 'common/conexio.php';

$pass = $_POST['pswd'];
$login = $_POST['username'];

if (empty($pass) OR empty($login) OR strstr($login, ' ') OR strstr($pass, ' ')) {
    echo "Error";
} else {
    $CLD_CON->OpenRs("SELECT * FROM CLD_Login WHERE username='$login' AND userType=4");
    if ($CLD_CON->FetchArray()) {
        $passw = $CLD_CON->GetArrayField("password");
        $passw = md5($pass);
        if ($passw = $pass) {
            echo "OK";
        } else {
            echo "Error";
        }
    } else {
        echo "Error";
    }
}
?>
