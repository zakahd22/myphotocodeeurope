<?php
require("common.php"); 
$f = fopen('./logPal.txt', 'a+');
fwrite ($f, "\r\n BdD: $APP_BdD_error;\r\n");
fwrite ($f, "\r\n okBase: $APP_BdD->okBase;\r\n");
fclose ($f);

$ara = new DateTime("now");
//$f = fopen('./util/logPal.txt', 'a+');
//fwrite ($f, "\r\n" . $ara->format("Ymd H:i:s") . "\r\n");
//fclose ($f);

//segons el codi propossat per pay pal

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}


$my_string = "";


// post back to PayPal system to validate
//canviPayPal  $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
//
//entorn real $header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";//canviPayPal
 $header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";//canviPayPal
 
 
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";

//entorn real  $header .= "Host: www.paypal.com\r\n";//20130110canviPayPal
$header .= "Host: www.sandbox.paypal.com\r\n";//20130110canviPayPal

$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
//?? $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
//$fp = fsockopen ('https://www.sandbox.paypal.com', "80", $errno, $errstr, 30);

$url_parsed=parse_url('https://www.sandbox.paypal.com/cgi-bin/webscr');
//entorn real  $url_parsed=parse_url('https://www.paypal.com/cgi-bin/webscr');

$fp = fsockopen ($url_parsed[host], "80", $errno, $errstr, 30);

//$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name']; $my_string.=  "\r\nitem_name : " . $_POST['item_name']  .  "\r\n";
$item_number = $_POST['item_number'];$my_string.=  "item_number: " . $_POST['item_number']  .  "\r\n";
$payment_status = $_POST['payment_status']; $my_string.=  "payment_status : " . $_POST['payment_status']  .  "\r\n";
$payment_amount = $_POST['mc_gross']; $my_string.=  "mc_gross : " . $_POST['mc_gross']  .  "\r\n";
$payment_currency = $_POST['mc_currency']; $my_string.=  "mc_currency : " . $_POST['mc_currency']  .  "\r\n";
$txn_id = $_POST['txn_id']; $my_string.=  "txn_id : " . $_POST['txn_id']  .  "\r\n";
$receiver_email = $_POST['receiver_email']; $my_string.=  "receiver_email : " . $_POST['receiver_email']  .  "\r\n";
$payer_email = $_POST['payer_email']; $my_string.=  "payer_email : " . $_POST['payer_email']  .  "\r\n";

if (!$fp) {
// HTTP ERROR
    
    $my_string.=  "\r\nfp = fsockopen  error ($errno) : $errstr\r\n";
} else {
    $my_string.=  "\r\nfp = fsockopen a ".$url_parsed[host]."  ok\r\n";
    
    
$ret = fputs ($fp, $header . $req);

$my_string.= "fputs returned $ret; req: $req\r\n";

$myRes = "";
$verified = false;
$invalid = false;
while (!feof($fp)) {
$res = fgets ($fp, 1024);
    $myRes.=  "$res\r\n";
}
fclose ($fp);


//?sandbox problems if (strcmp ($myRes, "VERIFIED") == 0) {

if (strcmp ($myRes, "VERIFIED") == 0  || $url_parsed[host] == 'www.sandbox.paypal.com' ) {
// check the payment_status is Completed
// check that txn_id has not been previously processed
// check that receiver_email is your Primary PayPal email
// check that payment_amount/payment_currency are correct
// process payment
    
$verified = true;

//20131016 INICI pot estar pending   
//20131016   if($payment_status == "Completed"){// tot pagat
if(($payment_status == "Completed") || ($payment_status == "Pending")){//  tot pagat o pending 
    
    if($payment_status == "Completed"){
       $fpagstatus = 4;
       $infoPending = "";
       $completed = "Completed";
    }
    else{
        $fpagstatus = 5;
       $infoPending = "<h2>The status of this payment is <b>pending</b> and is necessary to go into the account and accept it.</h2>";
       $completed = "Pending";
    }
//20131016 FINAL    


$idOrder ="unknown order id ";
$clientEmail ="unknown client email ";

    
$f = fopen('./logPal.txt', 'a+');
fwrite ($f, "\r\n" . $ara->format("Ymd H:i:s") . "Completed , my_string01: $my_string; myRes: $myRes;;\r\n");
fclose ($f);
    
    
//guardar-ho a la comanda
if((isset ($_POST['item_number']))){

    $lpos =  strpos ( $item_number , "-" );
    if ($lpos === false) {
        $idOrder = $item_number;
        $fpagn = 0;
        
    }
    else{
        $idOrder = substr($item_number, 0, $lpos);
        $fpagn = substr($item_number, $lpos+1);
    }

    
   
//no cal        include("../common/APP_BdD.php");
   //SELECT `idOrder`, `idOwner`, `when`, `shipping`, `total`, `idAdress`, `text`, `address`, `city`, `state`, `code`, `country`,`email`, `fpag`, `fpagcontrol`, `fpagn`, `fpagstatus` FROM `App_orders` WHERE 1

    $clientEmail = "";
    $sql ="SELECT `email`  FROM `App_orders` WHERE  idOrder=$idOrder AND `fpagn`=$fpagn;";
     
$f = fopen('./logPal.txt', 'a+');
fwrite ($f, "\r\n sql: $sql;;\r\n");
fclose ($f);
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
        $my_string.= "Error reading email: $sql\r\n";
    }
    else{
        $esOK = $APP_BdD->FetchRs();
        if(!$esOK){
            $my_string.= "Error order not found: $sql\r\n";
        }
        else{
            $clientEmail = $APP_BdD->GetField(1);
            $APP_BdD->CloseRs();
        }
    }
       
        
 //més control      $sql = "UPDATE `App_orders` SET `fpagstatus`=2 WHERE idOrder=$idOrder AND ntpv=$ntpv AND controltpv='$item_number';";
        $sql = "UPDATE `App_orders` SET `fpagstatus`=$fpagstatus WHERE idOrder=$idOrder AND `fpagn`=$fpagn;";
        $APP_BdD->Execute($sql);
    $my_string.=  "sql = $sql\r\n";
    
    //es pot enviar un email al client

//// INICI
//    $UNV_idioma = 'Es';
//    include("common/UNV_mailCompra.php");//ha d'enviar el mail de confirmaci� de compra
//
//// FINAL
//



}
// un email
$mail_email = "victor.carretero@treemes.com";//email a sales@dc-image.com
$mail_nom = "APP Owners";
$mail_subject = "PayPal Payment $completed from APP Owners";
$mail_cont = "<h1>An order from the APP has been payed with PayPal</h1>$infoPending<table>";
$mail_cont.= "<tr><td>Order id:</td><td><b>$idOrder</b></td></tr>";
$mail_cont.= "<tr><td>Client email:</td><td><b>$clientEmail</b></td></tr>";
$mail_cont.= "<tr><td>PayPal transaction id:</td><td><b>$txn_id</b></td></tr>";
$mail_cont.= "</table>";
include("../common/APP_mail.php");

}//20110728 end de if($payment_status == "Completed")
else{
    $my_string.=  "payment_status != Completed\r\n";
    //marquem la comanda
    if((isset ($_POST['item_number']))){

        $lpos =  strpos ( $item_number , "-" );
        if ($lpos === false) {
            $idOrder = $item_number;
            $fpagn = 0;

        }
        else{
            $idOrder = substr($item_number, 0, $lpos);
            $fpagn = substr($item_number, $lpos+1);
        }
        $sql = "UPDATE `App_orders` SET `fpagstatus`=3 WHERE idOrder=$idOrder;";
        $APP_BdD->Execute($sql);
    }
    $my_string.=  "sql = $sql\r\n";
    
    
// un email
$mail_email = "victor.carretero@treemes.com";//email a sales@dc-image.com
$mail_nom = "APP Owners";
$mail_subject = "PayPal Payment Not Completed from APP Owners";
$mail_cont = "<h1>There are problems with the payment PayPal of an order</h1><table>";
$mail_cont.= "<tr><td>Payment_status:</td><td><b>$payment_status</b></td></tr>";
$mail_cont.= "<tr><td>Order id:</td><td><b>$idOrder</b></td></tr>";
$mail_cont.= "<tr><td>Payment amount:</td><td><b>$payment_amount</b></td></tr>";
$mail_cont.= "<tr><td>Payment currency:</td><td><b>$payment_currency</b></td></tr>";
$mail_cont.= "<tr><td>PayPal transaction id:</td><td><b>$txn_id</b></td></tr>";
$mail_cont.= "</table>";


$mail_cont.= "<p>PayPal info:</p>";

$mail_cont.= $my_string;



include("../common/APP_mail.php");
    

}    

}
else if (strcmp ($myRes, "INVALID") == 0) {
// log for manual investigation
$invalid = true;

}
//}
//fclose ($fp);
}

if($verified) $my_string.=  "VERIFIED\r\n";
if($invalid) $my_string.=  "INVALID\r\n";



$f = fopen('./logPal.txt', 'a+');
fwrite ($f, "\r\n" . $ara->format("Ymd H:i:s") . "Res: $myRes;" . $my_string);
fclose ($f);


?>
