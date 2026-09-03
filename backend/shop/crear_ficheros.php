<?php
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

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

if (!$mail->send()) {
    $mail_ret = 0;
    utils::log($mail->retMsg, "logMailer", "crear_ficheros");
} else {
    $mail_ret = 1;
}
$mail_retMsg = ob_get_contents();
ob_end_clean();

}
require_once(G_PATH . "includes/classes/html2pdf/html2pdf.class.php");


$CLD_CON->OpenRs("SELECT * FROM SHP_Comandes WHERE id=$idOrder");
if($CLD_CON->FetchArray()) {
    $tt = 0;
    $id_c = $CLD_CON->GetArrayField("id");
    $fecha = $CLD_CON->GetArrayField("fecha");
    $f_date = date("d M,Y", strtotime($fecha));
    $a_id = $CLD_CON->GetArrayField("address");
    $c_id = $CLD_CON->GetArrayField("contact");
    $impost_c = $CLD_CON->GetArrayField("impostos");
    $shop_c = $CLD_CON->GetArrayField("shop");
    $currency = $CLD_CON->GetArrayField("currency");
    $printed = $CLD_CON->GetArrayField("printed");
    $CLD_CON3->OpenRs("SELECT * FROM SHP_currency WHERE id=$currency");
    if ($CLD_CON3->FetchArray()) {
        $currency_symbol = $CLD_CON3->GetArrayField("symbol_html");
    }

    $CLD_CON3->OpenRs("SELECT * FROM SHP_address WHERE id=$a_id");
    if ($CLD_CON3->FetchArray()) {
        $address = "<div style='width:100%;text-align:right;border:2px solid gray;padding: 20px;'>";
        $address .= "<p> " . $CLD_CON3->GetArrayField("Street") . ", " . $CLD_CON3->GetArrayField("Number") . "</p>";
        $address .= "<p> (" . $CLD_CON3->GetArrayField("zip") . ")" . $CLD_CON3->GetArrayField("City") . "</p>";
        $address .= "<p> " . $CLD_CON3->GetArrayField("State") . "</p></div>";
    }
    $CLD_CON3->OpenRs("SELECT * FROM SHP_Contacts WHERE id=$c_id");
    if ($CLD_CON3->FetchArray()) {
        $contact = "<div style='width:100%;text-align:left;border:2px solid gray;margin-top:10px;padding: 20px;margin-bottom:50px;'>";
        $contact.= "<p> " . $CLD_CON3->GetArrayField("Last_Name") . ", " . $CLD_CON3->GetArrayField("Name") . "</p>";
        $contact .= "<p>" . $CLD_CON3->GetArrayField("Phone") . "</p><p> " . $CLD_CON3->GetArrayField("email") . "</p></div>";
    }


    $CLD_CON2->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE comanda=$id_c");
    $html2 = "<table  border='1' style='padding:20px;margin:5px;'>";
    $html2 .= "<tr style='padding:20px;margin:5px;'><td>Code</td><td>Product</td><td>Qty</td><td>Unit/Price</td><td>Total</td></tr>";
    while ($CLD_CON2->FetchArray()) {
        $producte_id = $CLD_CON2->GetArrayField("producte");
        $qty = $CLD_CON2->GetArrayField("qty");
        $preu = $CLD_CON2->GetArrayField("preu");
        $photo = $CLD_CON2->GetArrayField("photoCode");
        
        $CLD_CON3->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.code='$photo'");

        if ($CLD_CON3->FetchArray()) {
            $date_e = $CLD_CON3->GetArrayField("start_date");
            $id_e = $CLD_CON3->GetArrayField("id");
            $img = "/events/$date_e$id_e/$photo.jpg";
        }
        $CLD_CON3->OpenRs("SELECT * FROM SHP_products WHERE id=$producte_id");
        if ($CLD_CON3->FetchArray()) {
            $shipping = $CLD_CON3->GetArrayField("shipping") * $qty;
            $p_name = $CLD_CON3->GetArrayField("name");
            $p_code = $CLD_CON3->GetArrayField("code");
            $p_des = $CLD_CON3->GetArrayField("descripcio");
            $style = $CLD_CON3->GetArrayField("style2");
            $image = $_SERVER['DOCUMENT_ROOT'] . "/images/shop/$p_code/1.png";
            $image = substr($image, strpos($image, "htdocs/") + 7);
        }

        $html2 .= "<tr style='padding:20px;margin:5px;'><td style='padding:20px;margin:5px;'> $p_code</td><td style='padding:20px;margin:5px;'> $p_name </td><td style='padding:20px;margin:5px;'>$qty</td><td style='padding:20px;margin:5px;'>$preu$currency_symbol</td><td style='padding:20px;margin:5px;'>" . ($qty * $preu) . "$currency_symbol</td></tr>";
        $tt = $tt + ($qty * $preu);
    }
    $imp = ($tt * $impost_c) / 100;
    $html2 .= "<tr style='padding:20px;margin:5px;' ><td colspan='3' style='padding:20px;margin:5px;'></td><td style='padding:20px;margin:5px;'>TAX $impost_c% </td><td style='padding:20px;margin:5px;'>" . number_format($imp, 2, ',', '') . "$currency_symbol</td></tr>";
    $html2 .= "<tr><td colspan='3' style='padding:20px;margin:5px;'></td><td style='text-align:right;'>SHIPPING</td><td  style='text-align:right;padding:20px;margin:5px;'>" . number_format($shipping, 2, ',', ' ') . "$currency_symbol</td></tr>";
    $html2 .= "<tr style='padding:20px;margin:5px;'><td colspan='3' style='padding:20px;margin:5px;'></td><td style='padding:20px;margin:5px;'>TOTAL </td><td style='padding:20px;margin:5px;'>" . number_format(($tt + $imp + $shipping), 2, ',', '') . "$currency_symbol</td></tr>";
    $html2 .= "</table>";
    $d = date("Ymd");
    if (!file_exists("comandes/$shop_c/$id_c/$d/$id_c.pdf")) {
        $html = "<html><head></head><body>";
        $html .= "<p>Order $id_c ,  $f_date</p>";
        $html .= "<div style='width:100%;'>";
        $html .= $address;
        $html .= $contact;
        $html .= "</div>";
        $html .= "<div style='width:100%;'>";
        $html .= $html2;
        $html .= "</div>";
        $html .= "</body></html>";
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
        $html2pdf->writeHTML($html);

        if (!file_exists("./comandes/$shop_c/$d")) {
            mkdir("./comandes/$shop_c/$d");
        }
        mkdir("./comandes/$shop_c/$d/$id_c");
        $output_file = "./comandes/$shop_c/$d/$id_c/$id_c.pdf";
        $html2pdf->Output($output_file, 'F');
        
        $CLD_CON2->OpenRs("SELECT e.start_date , e.id FROM events e LEFT JOIN photos p ON p.event_id=e.id WHERE p.code='$photo'");
        if($CLD_CON2->FetchArray()){
            $eID = $CLD_CON2->GetArrayField("start_date");
            $eDate = $CLD_CON2->GetArrayField("id");    
            copy($_SERVER['DOCUMENT_ROOT'] . "/events/$eDate$eID/$photo.jpg", $_SERVER['DOCUMENT_ROOT'] ."/shop/comandes/$shop_c/$d/$id_c/$photo.jpg");
        }
        
    }
  }
?>