<?php
require_once '../common/global.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

//$photo= "A9SXV3C2UJ";
$p_id = $_SESSION['product_id'];
$code = $_SESSION['product_code'];
$photo = $_SESSION['photo'];

$CLD_CON->OpenRs("SELECT * FROM SHP_products WHERE code='$code' AND id=$p_id");
if ($CLD_CON->FetchArray()) {
    $styles_p = "<style>".$CLD_CON->GetArrayField("style") ."</style>";
}



$c_nom = $_POST['name_Contact'];
$c_last = $_POST['last_Contact'];
$c_phone = $_POST['phone'];
$c_mail = $_POST['mail_Contact'];

$a_street = $_POST['street'];
$a_num = $_POST['num'];
$a_zip = $_POST['zip'];
$a_city = $_POST['city'];
$a_state = $_POST['state'];
$comanda_id = $_POST['comanda'];
$image = $_POST['image_p'];

if($a_state == 'Florida'){
    $impost = 7;
}else{
    $impost = 0;
}

$x = false;
$CLD_CON2->OpenRs("SELECT * FROM SHP_Comandes WHERE id=$comanda_id");
if ($CLD_CON2->FetchArray()) {
    $CLD_CON->Execute("UPDATE SHP_Comandes SET impostos=$impost  WHERE id=$comanda_id");
    $_SESSION['comanda'] = $comanda_id;
    $add = $CLD_CON2->GetArrayField("address");
    $contact = $CLD_CON2->GetArrayField("contact");
    $estat = $CLD_CON2->GetArrayField("estat");
    $currency = $CLD_CON2->GetArrayField("currency");
    $n2 = $CLD_CON2->GetArrayField("n2");
    $shop = $CLD_CON2->GetArrayField("shop");
    $CLD_CON->OpenRs("SELECT * FROM SHP_currency WHERE id=$currency");
   
    
    
    if ($CLD_CON->FetchArray()) {
        $cur_symbol = $CLD_CON->GetArrayField("symbol_html");
        $currency_code_paypal = $CLD_CON->GetArrayField("paypal_code");
    }
    if ($estat == 0) {
        if ($contact != "") {
            $CLD_CON->Execute("DELETE FROM SHP_Contacts WHERE id=$contact");
            $x = true;
        }
        if ($add != "") {
            $CLD_CON->Execute("DELETE FROM SHP_address WHERE id=$add");
            $x = true;
        }
        if ($x) {
            $CLD_CON->Execute("UPDATE SHP_Comandes SET n2= n2+1 WHERE id=$comanda_id");
        }
    }
}
if ($estat == 0) {
    $c_id = $CLD_CON->ExecuteInsert("INSERT INTO SHP_Contacts (Name , Last_Name , Phone , email) VALUES('$c_nom' , '$c_last' , '$c_phone' , '$c_mail')");
    $a_id = $CLD_CON->ExecuteInsert("INSERT INTO SHP_address (Street , Number , City , State , Country , Floor , zip) VALUES('$a_street' , '$a_num' , '$a_city' , '$a_state' , 'USA' , '' , '$a_zip')");
    $CLD_CON->Execute("UPDATE SHP_Comandes SET address=$a_id , contact=$c_id  WHERE id=$comanda_id");
}
?>
<html>
    <head>
        <?php 
        include 'head.php'; 
        echo $styles_p;
        ?>
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&language=en"></script>

    </head>
    <body>
        <?php
        include 'header.php';
        ?>
        <div class='blok blokWhite'>
            <?php
            echo "<div class='imgContainer'>";
            echo "<img  src='$image' class='taza'>";
            $CLD_CON2->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.code='$photo'");
            echo "<p style='color:transparent;'>Text Transparent</p>";
            if ($CLD_CON2->FetchArray()) {
                $date_e = $CLD_CON2->GetArrayField("start_date");
                $id_e = $CLD_CON2->GetArrayField("id");
                $img_url = "events/$date_e$id_e/$photo.jpg";
            }
            echo "<div class='imgTransform'>";
            echo "<img  src='$img_url'>";
            echo "</div>";
            echo "<div class='WhiteDiv'></div>";
            echo "<div class='WhiteDivLateral'></div>";
            echo "</div>";
            ?>
            <div  class='contentDiv'>
                <h2>Address</h2>
                <?php
                echo "<p>$a_street , $a_num , floor</p>";
                echo "<p>$a_city ($a_zip) , $a_state , USA</p>";
                ?>
                <h2>Contact</h2>
                <?php
                echo "<p>$c_last , $c_nom</p>";
                echo "<p>$c_phone , $c_mail</p>";
                ?>

                <h2> Order Info </h2>
                <table style='width:90%;text-align:center;margin-left:5%;' border="1">
                    <tr><td>Qty</td><td>Product</td><td>Unit Price</td><td>Total</td></tr>
                    <?php
                    $CLD_CON->OpenRs("SELECT producte ,  qty , preu FROM SHP_Comandes_Products WHERE comanda=$comanda_id");
                    $total = 0;
                    $shipping = 0;
                    $n = "";
                    while ($CLD_CON->FetchArray()) {
                        $p = $CLD_CON->GetArrayField("producte");
                        $q = $CLD_CON->GetArrayField("qty");
                        $preu_u = $CLD_CON->GetArrayField("preu");
                        $pr_total = $q * $preu_u;
                        $total = $total + $pr_total;
                        $CLD_CON2->OpenRs("SELECT name , code , shipping FROM SHP_products WHERE id=$p");
                        if ($CLD_CON2->FetchArray()) {
                            $p_name = $CLD_CON2->GetArrayField("name");
                            $p_code = $CLD_CON2->GetArrayField("code");
                            $p_shipping = $CLD_CON2->GetArrayField("shipping");
                        }
                        echo "<tr>";
                        echo "<td>$q</td>";
                        echo "<td>$p_code - $p_name</td>";
                        echo "<td  style='text-align:right;'>$preu_u" . "$cur_symbol</td>";
                        echo "<td  style='text-align:right;'>$pr_total" . "$cur_symbol</td>";
                        echo "</tr>";
                        $n .= "$p_code - $p_name(" . $q . "u.)";
                        $shipping = $shipping + ($p_shipping * $q);
                    }

                    echo "<tr><td colspan=2 style='text-alig:right;'></td><td></td><td  style='text-align:right;'>". number_format($total, 2, ',', ' ')  ."</td></tr>";
                    $e_imp = ($total * $impost) / 100;
                    $tt = $e_imp + $total;
                    $tt = $tt + $shipping;
                    echo "<tr><td colspan=2 style='text-align:right;'> IMPOSTOS</td><td style='text-align:right;'>$impost%</td><td  style='text-align:right;'>". number_format($e_imp, 2, ',', ' ') . "$cur_symbol</td></tr>";
                    echo "<tr><td colspan=2 style='text-align:right;'> SHIPPING</td><td style='text-align:right;'></td><td  style='text-align:right;'>" . number_format($shipping, 2, ',', ' ') ."$cur_symbol</td></tr>";
                    echo "<tr><td colspan=2 style='text-align:right;'> TOTAL</td><td></td><td  style='text-align:right;'>" .number_format($tt, 2, ',', ' '). "$cur_symbol</td></tr>";
                    ?>                   
                </table>
                <span id='formCont'></span>
                <?
                echo "<input type='button' value='Pay' onclick='payment()' id='payButton'>";
                //   echo "<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\" style='float: right;padding: 8%;'>
//<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">";
                ?>

            </div>
        </div>
        <script>
            function payment() {
                $("#payButton").attr("disabled", "disabled");
                $("#payButton").fadeTo(250, 0.25, function() {
                    $(this).css("cursor", "");
                });
                var ajaxData = {com: "<?php echo $comanda_id; ?>", n2: "<?php echo $n2; ?>", at: "<?php echo $tt; ?>", curr: "<?php echo $currency_code_paypal; ?>", productName: "<?php echo $n; ?>", s: "<?php echo $shop; ?>"};
                $.ajax({
                    url: 'paypalFormAjax.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data) {
                        $("#formCont").html(data);
                        $("#paypalform").submit();

                    },
                    // Form data
                    cache: false,
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });

            }
        </script>
    </body>

    <?php include 'footer.php'; ?>
</html>
