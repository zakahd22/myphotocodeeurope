<?php   
require_once 'common/global.php';
require_once G_PATH . "common/Classes/baseController.php";

header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
header ("Pragma: no-cache"); 

if(!(isset($_SESSION['USERTYPE'])) OR empty($_SESSION['USERTYPE'])){
    header("Location: ". G_PAGE ."index.php?error=Session+has+not+started" ) ;
}

$USERTYPE = $_SESSION['USERTYPE'];
if(isset($_SESSION['USERID'])){
    $USERID = $_SESSION['USERID'];
}
?>