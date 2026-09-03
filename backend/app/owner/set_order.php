<?php
//Parámetros:
//-	username (string)
//-	password (string) sin ningún tipo de encriptación
//-	pack1 (int) el nombre de parámetro se construye concatenando “pack” + <pack id>
//-	pack3 (int) y el valor será el número de unidades solicitadas
//-	total1 (decimal(2 decimales)) el nombre de parámetro se construye concatenando “total” + <pack id>
//-	total3 (decimal(2 decimales)) y el subtotal de precio x unidades
//-	shipping (decimal(2 decimales)) coste de shipping
//-	total (decimal(2 decimales)) total del pedido
//-	adress (int) id de la dirección seleccionada

//Mostra
//if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
//else{
//echo "$APP_xml<comm_status>$APPERROR_noid</comm_status></return>";
//return;
//}

require("common.php");

if(!$APP_user) return;


$nPaks = 0;

$sql = "SELECT `idPack`, `label`, `descr`, `price` FROM `App_ordersPack` WHERE active = 1; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

while($APP_BdD->FetchRs()){
    $id = $APP_BdD->GetField(1);
    $parametre = "pack" . $id;
    
//echo "TRACE parametre: $parametre\n";

    if(isset($_REQUEST[$parametre])){ 
        $array_labelPack[$nPaks] = $APP_BdD->GetField(2);
        $array_descrPack[$nPaks] = $APP_BdD->GetField(3);
        $array_pricePack[$nPaks] = $APP_BdD->GetField(4);
        $array_idPack[$nPaks] = $id;
        $array_units[$nPaks] = $_REQUEST[$parametre];
        $parametre = "total" . $id;
        if(isset($_REQUEST[$parametre])){
            $array_price[$nPaks] = $_REQUEST[$parametre];
        }
        else{
            echo "$APP_xml<comm_status>Error code: 0002 Order of pack $id don't have price</comm_status></return>";
            return;
            
        }
        $nPaks++;
    }
}
if(!$nPaks){
echo "$APP_xml<comm_status>Error code: 0003 No orders received</comm_status></return>";
return;
}

if(isset($_REQUEST['shipping'])){
    $shipping = $_REQUEST['shipping'];
}
else $shipping = 0;


if(isset($_REQUEST['total'])){
    $total = $_REQUEST['total'];
}
else {
    echo "$APP_xml<comm_status>Error code: 0004 No total amount</comm_status></return>";
    return;
}

if(isset($_REQUEST['adress'])){
    $adress = $_REQUEST['adress'];
}
else $adress = 0;


$ara = new DateTime("now");
$APP_araTimeSerial = $APP_BdD->myDateTimeSerial($ara);

//20130502 INICI

//address
$addrAddress = "";

$sql = "SELECT `address`, `code`, `city`, `state`, `country` FROM App_ownerAddress WHERE `id`=$adress ; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0008 </comm_status></return>";
    return;
}
$mail_adress = "<table>";
if($APP_BdD->FetchRs()){
//    $mail_cont.= "<tr><td colspan='3'>Address:</td></tr>";
    $tmp =  $APP_BdD->GetField(1);
    $mail_adress.= "<tr><td>Address:</td><td colspan='2'><b>$tmp</b></td></tr>";
    if($tmp) $addrAddress.= ",`address` = '". str_replace("'","''",$tmp). "'";
    
    $tmp =  $APP_BdD->GetField(2);
    $mail_adress.= "<tr><td>Zip Code:</td><td colspan='2'><b>$tmp</b></td></tr>";
    if($tmp) $addrAddress.= ",`code` = '". str_replace("'","''",$tmp). "'";
    $tmp =  $APP_BdD->GetField(3);
    $mail_adress.= "<tr><td>City:</td><td colspan='2'><b>$tmp</b></td></tr>";
    if($tmp) $addrAddress.= ",`city` = '". str_replace("'","''",$tmp). "'";
    $tmp =  $APP_BdD->GetField(4);
    $mail_adress.= "<tr><td>State:</td><td colspan='2'><b>$tmp</b></td></tr>";
    if($tmp) $addrAddress.= ",`state` = '". str_replace("'","''",$tmp). "'";
    $tmp =  $APP_BdD->GetField(5);
    $mail_adress.= "<tr><td>Country:</td><td colspan='2'><b>$tmp</b></td></tr>";
    if($tmp) $addrAddress.= ",`country` = '". str_replace("'","''",$tmp). "'";
}
else{
$mail_adress.= "<tr><td colspan='3'>No address information!</td></tr>";

}
$mail_adress.= "</table>";

//20130502 FINAL

//20130502controlcomanda INICI
   //SELECT `idOrder`, `idOwner`, `when`, `shipping`, `total`, `idAdress`, `text`, `address`, `city`, `state`, `code`, `country`,`email`, `fpag`, `fpagcontrol`, `fpagn`, `fpagstatus` FROM `App_orders` WHERE 1

$fpagcontrol = rndm32(15);
$controlcomanda = ", `fpagcontrol` = '$fpagcontrol'";

//20130502controlcomanda FINAL


//insert a orders `idOrder`, `idOwner`, `when`, `shipping`, `total`, `idAdress`, `text`
//20130502 $sql = "INSERT INTO App_orders SET idOwner=$APP_userId, `when`=$APP_araTimeSerial, `shipping`=$shipping, `total`=$total, `idAdress`=$adress ;";
//20130605 també email $sql = "INSERT INTO App_orders SET idOwner=$APP_userId, `when`=$APP_araTimeSerial, `shipping`=$shipping, `total`=$total, `idAdress`=$adress $addrAddress $controlcomanda ;";//20130502
 

$sql = "INSERT INTO App_orders SET idOwner=$APP_userId, `when`=$APP_araTimeSerial, `shipping`=$shipping, `total`=$total, `idAdress`=$adress $addrAddress ,`email`='$APP_user_email' $controlcomanda ;";//20130502



 $idOrder = $APP_BdD->ExecuteInsert($sql);
 if(!$idOrder) {
     echo "Error - code: 0005 Database insert: $sql.";
     return;

 }
 $xmlcontrolcomanda = "<order_id>$idOrder</order_id>";
 $xmlcontrolcomanda.= "<pay_url>app/pay/OrderPal?p=$fpagcontrol$idOrder</pay_url>";
 
 // a orders SELECT `id`, `idOrder`, `idPack`, `units`, `price`, `shipping` FROM `App_oderLines` WHERE 1
 for($i=0;$i<$nPaks;$i++){
    $sql = "INSERT INTO App_oderLines SET idOrder=$idOrder, `idPack`={$array_idPack[$i]}, `units`={$array_units[$i]}, `price`={$array_price[$i]} ;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
     echo "Error - code: 0006 Database insert: $sql.";
     return;
    }
   
 }

// un email
$mail_email = "mon@dc-image.com";
$mail_nom = "Montserrat";
$mail_copia1 = "jtarres@dc-image.com";
$mail_copianom1 = "Josep";
$mail_subject = "APP owners initiated a Purchase";
$mail_cont = "<h1>Initiated Purchase from the PhotoBooth APP</h1>";
$mail_cont.= "<h2>(pending to pay with Pay Pal)</h2>";

$laComanda = "<table>";

$laComanda.= "<tr><td>Client name:</td><td><b>$APP_user</b></td></tr>";
$laComanda.= "<tr><td>Client email:</td><td><b>$APP_user_email</b></td></tr>";
$laComanda.= "<tr><td>Order id:</td><td><b>$idOrder</b></td></tr>";
$laComanda.= "</table><p>Purchase:</p><table border='1' cellspacing='0' cellpadding='5'>";



//20140410  $laComanda.= "<tr><td align='right'>Pack id</td><td>Label</td><td>Description</td><td align='right'>Price/u</td><td align='right'>Units</td><td align='right'>Price</td></tr>";
$laComanda.= "<tr><td>Label</td><td>Description</td><td align='right'>Price/u</td><td align='right'>Units</td><td align='right'>Price</td></tr>";//20140410

//        $array_labelPack[$i] = $APP_BdD->GetField(2);
//        $array_descrPack[$i] = $APP_BdD->GetField(3);
//        $array_pricePack[$i] = $APP_BdD->GetField(4);

 for($i=0;$i<$nPaks;$i++){
//20140410     $laComanda.= "<tr><td align='right'><b>{$array_idPack[$i]}</b></td><td>$array_labelPack[$i]</td><td>$array_descrPack[$i]</td><td align='right'>$array_pricePack[$i]</td><td align='right'><b>{$array_units[$i]}</b></td><td align='right'><b>{$array_price[$i]}</b></td></tr>";
     $laComanda.= "<tr><td>$array_labelPack[$i]</td><td>$array_descrPack[$i]</td><td align='right'>$array_pricePack[$i]</td><td align='right'><b>{$array_units[$i]}</b></td><td align='right'><b>{$array_price[$i]}</b></td></tr>";//20140410
 }
//20140410   $laComanda.= "<tr><td colspan='5'>Shipping:</td><td align='right'><b>$shipping</b></td></tr>";
//20140410   $laComanda.= "<tr><td colspan='5'><b>Total:</b></td><td align='right'><b>$total</b></td></tr>";
$laComanda.= "<tr><td colspan='4'>Shipping:</td><td align='right'><b>$shipping</b></td></tr>";//20140410   
$laComanda.= "<tr><td colspan='4'><b>Total:</b></td><td align='right'><b>$total</b></td></tr>";//20140410   
$laComanda.= "</table><p>Shipping Address:</p>";
$laComanda.= $mail_adress;

$mail_cont.= $laComanda;

 
 
 
$mail_nomremitent = "DC PhotoBooth APP";

include("../common/APP_mail.php");

if(!$mail_ret){
    echo "$APP_xml<comm_status>Error Order inserted but mail not sent</comm_status></return>";
    return;
    
}
 
 //guardem el contingut a cont
 $sql = "UPDATE App_orders SET cont='". str_replace("'","''",$laComanda)."' WHERE idOrder=$idOrder";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
     $xmlcontrolcomanda.= "<TRACE>Can't update: $sql.</TRACE>";
     return;
    }

//20130502  echo "$APP_xml$APP_xmlOKcomm</return>"; // de moment no fem res més

echo "$APP_xml$APP_xmlOKcomm$xmlcontrolcomanda</return>";//20130502  
?>
