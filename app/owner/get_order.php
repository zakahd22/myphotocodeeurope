<?php
require("common.php");

/* param id
 * 
 */


if(!$APP_user) return;

if(isset($_REQUEST['id'])){ $idOrder = $_REQUEST['id'];}
else{
echo "$APP_xml<comm_status>$APPERROR_noid</comm_status></return>";
return;
}

$xml = $APP_xmlOKcomm;


$sql ="SELECT `when`, `total`, `fpagstatus`  FROM `App_orders` WHERE  idOrder = $idOrder;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}
$xml.= "<order>";
if($APP_BdD->FetchRs()){
    $xml.= "<order_date>";
    $tmp = $APP_BdD->GetFieldDateTime(1);
    if($tmp){
     $xml.= $tmp->format("m-d-Y H:i");
    }
    $xml.= "</order_date>";
    $tmp =  $APP_BdD->GetField(2);
    $xml.= "<order_amount>$tmp</order_amount>";
    $tmp =  $APP_BdD->GetField(3);
    $xml.= "<order_status>$tmp</order_status>";
    $APP_BdD->CloseRs();
}
$xml.= "</order>";

echo "$APP_xml$xml</return>"; // no cal res més



?>
