<?php

// Body of the html, pàgina OrderPal.php
        include("../common/APP_BdD.php");
        if(!$APP_BdD){
            echo "<p>There are problems in the order.</br>Code 01.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
            return;
        }
        $ara = new DateTime("now");
        
        if(isset($_REQUEST['p'])){
            $p = $_REQUEST['p'];
        }
        else {
            echo "<p>There are problems in the order.</br>Code 02.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
            return;
        }
        
        $l = strlen($p);
        if($l < 20){
            echo "<p>There are problems in the order.</br>Code 03.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
            return;
        }
        $fpagcontrol = substr($p, 0, 20);
        $idOrder = substr($p, 20);
        
        //ara comprovar que existeix a la BdD
      
//20131018    $sql ="SELECT `when`, `total`, `currency`, payPalVendor, `fpag`, `fpagcontrol`, `fpagn`, `fpagstatus`, idPB FROM `App_PBorders` WHERE  idOrder = $idOrder AND `fpagcontrol` = '$fpagcontrol';";
    $sql ="SELECT `when`, `total`, `currency`, payPalVendor, `fpag`, `fpagstatus`, idPB FROM `App_PBorders` WHERE  idOrder = $idOrder AND `fpagcontrol` = '$fpagcontrol';";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
        echo "TRACE ERROR $sql";
//        echo "<p>There are problems in the order. </br>Code 0301.</p>";
//        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
        return;
    }
    $esOK = $APP_BdD->FetchRs();
    if(!$esOK){
            echo "<p>There are problems in the order. Order not found</br>Code 04.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
            return;
    }
    $when = $APP_BdD->GetFieldDateTime(1);
    $total =  $APP_BdD->GetField(2);
    $currency =  $APP_BdD->GetField(3);
    $payPalVendor =  $APP_BdD->GetField(4);
    $fpagn =  $APP_BdD->GetField(5);
    $fpagstatus =  $APP_BdD->GetField(6);
    $idPB =  $APP_BdD->GetField(7);
    $APP_BdD->CloseRs();
    
//·	0: iniciat, pendent de pagar
//·	1: cancel·lada des del PB 
//·	2: pagament iniciat
//·	3: pagament ko, cancel·lat per l'usuari o compte erròni
//·	4: pagament ok, finalitzat correctament
    
    
    //comproven status
    switch($fpagstatus){
        case 0://començada és OK, però ens caldrà comprovar més coses (la data)
            break;
        default:
            echo "<p>The order has been already managed, status: $fpagstatus.</br>Code 05.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
            return;
    }
    //la data, totes dues dates són sobr eel rellotge del servidor
//ver 5.2    $interval = $ara->diff($when);
//ver 5.2    if($interval->invert){
    $hores = intval($ara->format('YmdH')) - intval($when->format('YmdH'));//ver 5.2
    
//20131018 desactivem aquest control per a poder fer proves INICI    
//.    if($hores<0){
//        //la comanda guardada té una data del futur, de fet hauria de ser un error
//        echo "<p>There are problems in the order.</br>Code 06.</p>";
//        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
//        return;
//    }
//20131018 desactivem aquest control per a poder fer proves FINAL    
//ver 5.2     if($interval->h > 1){
    if($hores > 1){
        //la comanda guardada ja fa una hora que està creada
        echo "<p>There are problems in the order. Order has expired.</br>Code 07.</p>";
        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
        return;
    }
    //import positiu
    if($total <= 0){
        echo "<p>There are problems in the order. Incorrect amount.</br>Code 08.</p>";
        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoPBPal.php';},3000);</script>";
        return;
    }
    
    //sembla que ja podem procesar!!!
    
    
    //es faran servir els de PayPal
    $currency_code = $currency;
    
    
    //per a que PayPal no es queixi si s'el crida dos cops amb la mateixa referència
    //de moment no ho farem servir
//    if(!$fpagn) $fpagn = 1;
//    else $fpagn++;
    
  //? $fpagn++;
   //?      $sql = "UPDATE `App_orders` SET `fpagstatus`=2, `fpagn`=$fpagn WHERE idOrder=$idOrder;";
        $sql = "UPDATE `App_PBorders` SET `fpagstatus`=2 WHERE idOrder=$idOrder;";
        $APP_BdD->Execute($sql);

    
    //    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
    //    $emailSeller = "??@dc-image.com";//20110526
    //    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
     //   $emailSeller = "victor.carretero@treemes.com";
//20140314        $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    //    $emailSeller = "victor.carretero-facilitator@treemes.com";
//20140314        $emailSeller = $payPalVendor;
    
    
    
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";//20140314
    $emailSeller = "2UUQDJ7DL2UTS";//20140314
    
    
    //form de crida a PayPal

echo "<h2>Amount in $currency_code: $total</h2>";
   
    //tret del form: <input onclick=\"\" type=\"submit\" value=\"Pay\"/>
echo "
<form id='paypalform'name='paypalform' action=\"$paypalurl\" method=\"post\">
<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
<input type=\"hidden\" name=\"business\" value=\"$emailSeller\">
<input type=\"hidden\" name=\"notify_url\" value=\"https://www.myphotocode.com/app/pay/CheckPBPal.php\">
<input type=\"hidden\" name=\"cancel_return\" value=\"https://www.myphotocode.com/app/pay/KoPBPal.php\">
<input type=\"hidden\" name=\"return\" value=\"https://www.myphotocode.com/app/pay/OkPBPal.php\">
<input type=\"hidden\" name=\"lc\" value=\"EN\">
<input type=\"hidden\" name=\"item_name\" value=\"PhotoBooth\">
<input type=\"hidden\" name=\"item_number\" value=\"$idOrder\">
<input type=\"hidden\" name=\"amount\" value=\"$total\">
<input type=\"hidden\" name=\"currency_code\" value=\"$currency_code\">
<input type=\"hidden\" name=\"button_subtype\" value=\"services\">
<input type=\"hidden\" name=\"address_override\" value=\"1\">
<input type=\"hidden\" name=\"no_note\" value=\"0\">
<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest\">

<input type=\"hidden\" name=\"no_shipping\" value=\"1\">

<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">


</form>

<!-- <script type=\"text/javascript\">document.forms[\"paypalform\"].Submit();</script> -->
<script type=\"text/javascript\">
function goPay () {
    var myF = document.getElementById(\"paypalform\");
    myF.submit();
}
//no window.onload = goPay;
</script>
        
                ";

//<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/WEBSCR-640-20110429-1/es_ES/i/scr/pixel.gif\" width=\"1\" height=\"1\">


//de moment enviem un email de control INICI


// un email
$mail_email = "victor.carretero@treemes.com";//email a sales@dc-image.com
$mail_nom = "myphotocode.com";
$mail_subject = "Control of Online Payment from a PhotoBooth";
$mail_cont = "<h1>An order from a PhotoBooth has been initiated</h1><table>";
$mail_cont.= "<tr><td>Order id:</td><td colspan='2'><b>$idOrder</b> (pending to pay with Pay Pal)</td></tr>";
$mail_cont.= "<tr><td>PhotoBooth id:</td><td><b>$idPB</b></td></tr>";
$mail_cont.= "<tr><td>Payment amount:</td><td><b>$total</b></td></tr>";
$mail_cont.= "<tr><td>Payment currency:</td><td><b>$currency_code</b></td></tr>";
 
$mail_cont.= "<tr><td colspan='3'>&nbsp;</td></tr>";

 
//client
//$sql = "SELECT `name` FROM rentals WHERE `id`=$adress ; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
//    //caldria controlar l'error
//    echo "$APP_xml<comm_status>Error Database error code: 0007 </comm_status></return>";
//    return;
//}
//if($APP_BdD->FetchRs()){
//    $tmp =  $APP_BdD->GetField(1);
//    $mail_cont.= "<tr><td>Client name:</td><td colspan='2'><b>$tmp</b></td></tr>";
//}
//else{
//$mail_cont.= "<tr><td colspan='3'>No client information!</td></tr>";
//
//}

$mail_cont.= "<tr><td colspan='3'>&nbsp;</td></tr>";

//20130502 INICI
$mail_cont.= $mail_adress;
////address
//$sql = "SELECT `address`, `code`, `city`, `state`, `country` FROM App_ownerAddress WHERE `id`=$adress ; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
//    //caldria controlar l'error
//    echo "$APP_xml<comm_status>Error Database error code: 0008 </comm_status></return>";
//    return;
//}
//if($APP_BdD->FetchRs()){
////    $mail_cont.= "<tr><td colspan='3'>Address:</td></tr>";
//    $tmp =  $APP_BdD->GetField(1);
//    $mail_cont.= "<tr><td>Address:</td><td colspan='2'><b>$tmp</b></td></tr>";
//    $tmp =  $APP_BdD->GetField(2);
//    $mail_cont.= "<tr><td>Code:</td><td colspan='2'><b>$tmp</b></td></tr>";
//    $tmp =  $APP_BdD->GetField(3);
//    $mail_cont.= "<tr><td>City:</td><td colspan='2'><b>$tmp</b></td></tr>";
//    $tmp =  $APP_BdD->GetField(4);
//    $mail_cont.= "<tr><td>State:</td><td colspan='2'><b>$tmp</b></td></tr>";
//    $tmp =  $APP_BdD->GetField(5);
//    $mail_cont.= "<tr><td>Country:</td><td colspan='2'><b>$tmp</b></td></tr>";
//}
//else{
//$mail_cont.= "<tr><td colspan='3'>No address information!</td></tr>";
//
//}
//20130502 FINAL

 $mail_cont.= "</table>";
include("../common/APP_mail.php");

if(!$mail_ret){
    echo "$APP_xml<comm_status>Error Order inserted but mail not sent</comm_status></return>";
    return;
    
}




//de moment enviem un email de control FINAL

?>
