<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
date_default_timezone_set('UTC');


$coment = addslashes($_POST['coment']);
 $ID = $_POST['id'];
$dateTime = date("Y-m-d H:i:s");
$uID = $_SESSION['USERID'];
$uT = $_SESSION['USERTYPE'];
$CLD_CON->OpenRs("SELECT username FROM CLD_Login WHERE id_user= $uID AND userType=$uT");
if($CLD_CON->FetchArray()){
    $username = $CLD_CON->GetArrayField("username");
}
$code = "#userin";
$status = 0;

if($CLD_CON->Execute("INSERT INTO CLD_Inc_coments (coment , incident, datetime , user)" . "VALUES( '$coment' , $ID , '$dateTime' , '$username')")){
    echo "OK";
}else{
    echo "ERROR";
    print_r(error_get_last());
}
?>