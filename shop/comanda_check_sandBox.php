<?php

include '../conf.php';
include '../conexio.php';

foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}
$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";                    // HTTP POST request
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$url_parsed=parse_url('https://www.sandbox.paypal.com/cgi-bin/webscr');
//entorn real  $url_parsed=parse_url('https://www.paypal.com/cgi-bin/webscr');

$fp = fsockopen('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
fputs($fp, $header . $req);
$email_bool = false;

while (!feof($fp)) {                     // While not EOF
    $res = fgets($fp, 1024);               // Get the acknowledgement response
    if (strcmp($res, "VERIFIED") == 0 || $url_parsed[host] == 'www.sandbox.paypal.com') {
        $verified = true;
        
        if(($payment_status == "Completed") || ($payment_status == "Pending")){//  tot pagat o pending 
            $email_bool = true;
        if ($payment_status == "Completed") {
            $fpagstatus = 2;
            //$infoPending = "";
            $completed = "Completed";
            
        } else {
            $fpagstatus = 1;
            //  $infoPending = "<h2>The status of this payment is <b>pending</b> and is necessary to go into the account and accept it.</h2>";
            $completed = "Pending";
        }

        if ((isset($_POST['item_number']))) {
            $lpos = strpos($item_number, "-");
            if ($lpos === false) {
                $idOrder = $item_number;
                $fpagn = 1;
            } else {
                $idOrder = substr($item_number, 0, $lpos);
                $fpagn = substr($item_number, $lpos + 1);
            }
            $CLD_CON->Execute("UPDATE SHP_Comandes SET estat=$fpagstatus WHERE id=$idOrder AND n2=$fpagn");
            include 'crear_ficheros.php';
            
        }
    } else {
        if ((isset($_POST['item_number']))) {

            $lpos = strpos($item_number, "-");
            if ($lpos === false) {
                $idOrder = $item_number;
                $fpagn = 1;
            } else {
                $idOrder = substr($item_number, 0, $lpos);
                $fpagn = substr($item_number, $lpos + 1);
            }

            $CLD_CON->Execute("UPDATE SHP_Comandes SET estat=3 WHERE id=$idOrder AND n2=$fpagn");
        }
    }
}else if (strcmp ($myRes, "INVALID") == 0) {

}

}

if($email_bool){
$CLD_CON->OpenRs("SELECT  * FROM SHP_Comandes WHERE id=$idOrder");
if ($CLD_CON->FetchArray()) {
    $contact_id = $CLD_CON->GetArrayField("contact");   
}

$CLD_CON->OpenRs("SELECT * FROM SHP_Contacts WHERE id=$contact_id");
if ($CLD_CON->FetchArray()) {
    $customer_names = $CLD_CON->GetArrayField("Name") . " " . $CLD_CON->GetArrayField("Last_Name");
    $to = $CLD_CON->GetArrayField("email");
    $to_str= $customer_names;
}
$CLD_CON->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE comanda=$idOrder");
if ($CLD_CON->FetchArray()) {
    $photoCode = $CLD_CON->GetArrayField("photoCode");

}
$mail_retMsg="";

//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml        
$mail_ret = 0;
//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        

ob_start();

require_once(G_PATH . 'common/mail.php');

$mail = new mail();

$mail->addAdress($to, $to_str);
$mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");

$mail->setSubject("MYPHOTOCODE SHOP");

$mail->setTemplate(G_PATH . "includes/emails/newOrder.html");

$mail->addTemplateField("###NAME###", $customer_names);
$mail->addTemplateField("###COMANDAID###", $idOrder);
$mail->addTemplateField("###PHOTOCODE###", $photoCode);

$mail->applyTempplateFields();

if (!$mail->Send()) {
    $mail_ret = 0;
} else {
    $mail_ret = 1;
}
$mail_retMsg = ob_get_contents();
ob_end_clean();

}
$xxxxx = print_r(error_get_last());
$fp2 = fopen("Comanda_Proves2.txt" , 'w');
fwrite($fp2 , "Hola em dic koko");
fwrite($fp2 , $xxxxx . "");
fclose($fp2);

$fp = fopen("Comanda_Proves.txt" , 'w');
fwrite($fp , $message. "<br>");
fwrite($fp, $mail_retMsg . "<br>");
fwrite($fp , $to . "<br>");
fclose($fp);
?>
