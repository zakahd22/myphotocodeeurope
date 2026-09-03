<?php

// Body of the html, pàgina OrderPal.php
        include("../common/APP_BdD.php");
        if(!$APP_BdD){
            echo "<p>There are problems in the order.</br>Code 01.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
            return;
        }
        $ara = new DateTime("now");
        
        if(isset($_REQUEST['p'])){
            $p = $_REQUEST['p'];
        }
        else {
            echo "<p>There are problems in the order.</br>Code 02.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
            return;
        }
        
        $l = strlen($p);
        if($l < 15){
            echo "<p>There are problems in the order.</br>Code 03.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
            return;
        }
        $fpagcontrol = substr($p, 0, 15);
        $idOrder = substr($p, 15);
        
        //ara comprovar que existeix a la BdD
        
    $sql ="SELECT `when`, `total`, `fpagn`, `fpagstatus`  FROM `App_orders` WHERE  idOrder = $idOrder AND `fpagcontrol` = '$fpagcontrol';";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
        //echo "TRACE ERROR $sql";
        echo "<p>There are problems in the order. </br>Code 0301.</p>";
        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
        return;
    }
    $esOK = $APP_BdD->FetchRs();
    if(!$esOK){
            echo "<p>There are problems in the order. Order not found</br>Code 04.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
            return;
    }
    $when = $APP_BdD->GetFieldDateTime(1);
    $total =  $APP_BdD->GetField(2);
    $fpagn =  $APP_BdD->GetField(3);
    $fpagstatus =  $APP_BdD->GetField(4);
    $APP_BdD->CloseRs();
    
//·	0: iniciat, pendent de pagar
//·	1: cancel·lada des de APP (de moment no hi ha cap mètode per a fer-ho)
//·	2: pagament iniciat
//·	3: pagament ko, cancel·lat per l'usuari o compte erròni
//·	4: pagament ok, finalitzat correctament
    

    //comproven status
    switch($fpagstatus){
        case 0://començada és OK, però ens caldrà comprovar més coses (la data)
            break;
        default://
            echo "<p>The order has been already managed, status: $fpagstatus.</br>Code 05.</p>";
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
            return;
    }
    //la data, totes dues dates són sobr eel rellotge del servidor
//ver 5.2    $interval = $ara->diff($when);
//ver 5.2    if($interval->invert){
    $hores = intval($ara->format('YmdH')) - intval($when->format('YmdH'));//ver 5.2
    if($hores<0){
        //la comanda guardada té una data del futur, de fet hauria de ser un error
        echo "<p>There are problems in the order.</br>Code 06.</p>";
        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
        return;
    }
//ver 5.2     if($interval->h > 1){
    if($hores > 1){
        //la comanda guardada ja fa una hora que està creada
        echo "<p>There are problems in the order. Order has expired.</br>Code 07.</p>";
        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
        return;
    }
    //import positiu
    if($total <= 0){
        echo "<p>There are problems in the order. Incorrect amount.</br>Code 08.</p>";
        echo "<script type=\"text/javascript\">setTimeout(function(){location.href='KoOrderPal.php';},3000);</script>";
        return;
    }
    
    //sembla que ja podem procesar!!!
    
    $fpagn++;
        $sql = "UPDATE `App_orders` SET `fpagstatus`=2, `fpagn`=$fpagn WHERE idOrder=$idOrder;";
        
//echo "TRACE $sql";        
        $APP_BdD->Execute($sql);

  
    //per a que PayPal no es queixi si s'el crida dos cops amb la mateixa referència
    //de moment no ho farem servir
//    if(!$fpagn) $fpagn = 1;
//    else $fpagn++;

    
//    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
//    $emailSeller = "??@dc-image.com";//20110526
//    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
 //   $emailSeller = "victor.carretero@treemes.com";
//20131016    $emailSeller = "victor.carretero-facilitator@treemes.com";
        
//20140313 INICI
        //NOTA: al compte de paypal de DC no trobo cap business id i el eMail és del Josep,
        // pel que faré servir <input type="hidden" name="hosted_button_id" value="TF3GN5DL8Z9YU"> al formulari
        
        //20140314, doncs no funciona el hosted_button_id, ja tinc el Merchant ID: 2UUQDJ7DL2UTS



//20140313 <input type=\"hidden\" name=\"business\" value=\"$emailSeller\">

//20140313    $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
//20140313     $emailSeller = "victor_1305578620_biz@treemes.com";//20131016
    
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";//20140313
    $emailSeller = "2UUQDJ7DL2UTS";//20140314
    
    //form de crida a PayPal
echo "
    
    <p>Film order id: $idOrder. Amount &#36; $total.&nbsp;</p>
<form id='paypalform'name='paypalform' action=\"$paypalurl\" method=\"post\">
<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
<input type=\"hidden\" name=\"business\" value=\"$emailSeller\">
<input type=\"hidden\" name=\"notify_url\" value=\"https://www.myphotocode.com/app/pay/CheckOrderPal.php\">
<input type=\"hidden\" name=\"cancel_return\" value=\"https://www.myphotocode.com/app/pay/KoOrderPal.php\">
<input type=\"hidden\" name=\"return\" value=\"https://www.myphotocode.com/app/pay/OkOrderPal.php\">
<input type=\"hidden\" name=\"lc\" value=\"EN\">
<input type=\"hidden\" name=\"item_name\" value=\"Film order\">
<input type=\"hidden\" name=\"item_number\" value=\"$idOrder-$fpagn\">
<input type=\"hidden\" name=\"amount\" value=\"$total\">
<input type=\"hidden\" name=\"currency_code\" value=\"USD\">
<input type=\"hidden\" name=\"button_subtype\" value=\"services\">
<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest\">

<input type=\"hidden\" name=\"address_override\" value=\"1\">
<input type=\"hidden\" name=\"no_shipping\" value=\"1\">

<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">

 

</form>
    <p>Please wait while the payment is directed to Paypal</p>

<!-- <script type=\"text/javascript\">document.forms[\"paypalform\"].Submit();</script> -->
<script type=\"text/javascript\">
function goPay () {
    var myF = document.getElementById(\"paypalform\");
    myF.submit();
}
window.onload = goPay;
</script>
 ";
//alert(myF);
//
//  <input onclick=\"\" type=\"submit\" value=\"Pay\"/>
//<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/WEBSCR-640-20110429-1/es_ES/i/scr/pixel.gif\" width=\"1\" height=\"1\">


?>
