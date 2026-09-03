<?php   
require_once '../common/global.php';

if(!(isset($_SESSION['USERTYPE'])) OR empty($_SESSION['USERTYPE'])){
header( "Location: ./index.php?error=1" ) ;
}
$userType = $_SESSION['USERTYPE'];
if($userType==1){
$_SESSION['USERID']= 9999991;
}
//$_SESSION['USERID']= 9999991;
?>