<?php
$APP_register = true;
require("common.php");


$APP_xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?"."><return>";
$APP_xmlOKcomm = "<comm_status>OK</comm_status>";
$ret = "$APP_xml$APP_xmlOKcomm</return>"; // no cal res més

if(isset($_REQUEST['username'])){
//    $user = $_REQUEST['username'];
    $user = str_replace("'","",$_REQUEST['username']);
    $sql = "SELECT id FROM Appusr_user WHERE username='$user'; ";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
        echo "$APP_xml<comm_status>Error - Database error code: 0001</comm_status></return>";
        return;
    }
    if($APP_BdD->FetchRs()){
        $ret = "$APP_xml<comm_status>Error - Username not available</comm_status></return>";
    }
    $APP_BdD->CloseRs();
}
else{
    echo "$APP_xml<comm_status>Error - No username param</comm_status></return>";
    return;

}
echo $ret;
?>
