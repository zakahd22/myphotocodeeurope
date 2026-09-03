<?php
require("common.php"); 
// echo "TRACE REMOTE_HOST: ".$_SERVER['REMOTE_HOST'];
// echo "TRACE REMOTE_ADDR: ".$_SERVER['REMOTE_ADDR'];
// return;

 
fesLog("REMOTE_HOST: ".$_SERVER['REMOTE_HOST']);
fesLog("REMOTE_ADDR: ".$_SERVER['REMOTE_ADDR']);
if(isset($_REQUEST['ctrl'])){
    fesLog("Control: ".$_REQUEST['ctrl']);
 echo "Ctrl: "  . $_REQUEST['ctrl'];
}
else{
 echo "Ctrl: none";

}
?>
