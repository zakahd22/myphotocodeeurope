<?php
//innclude '../sessio.php';
include '../conf.php';
include '../conexio.php';
require_once("../2.0/includes/classes/html2pdf/html2pdf.class.php");
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$comanda_get = $_GET['comanda'];
?>
<html>

    <head>
        <?php include 'head.php'; ?>
        <style>
            .comanda{
                width:90%;
                margin-left:5%;
                border: 2px solid gray;
                border-radius: 20px;
                margin-top: 2%;
                padding-bottom: 3%;
            }
            .comanda p {
                width:90%;
                margin-left:5%;
                margin-bottom:3%;
                margin-top:1%;
                font-weight: bold;
                font-size: 13pt;
            }
            .productMiniShop{
                position: relative;
                display: inline;
                float: left;
                width: 50%;
                margin-left:25%;
            }
            .imgProduct{
                position: absolute;        
                width:100%;
                top: 0;
                left: 0;
                z-index: 2;
            }
            .photoMini{
                position:relative;
                z-index:1;
            }
            .blokWhite{
                min-height: 80%;
                margin-top: 5%;
            }
        </style>
    </head>
    <body>
        <div class='blok blokWhite' id="c" >
            <?php
            echo "<h2 class='titles'>No orders printed: </h2>";
            $CLD_CON->OpenRs("SELECT * FROM SHP_Comandes WHERE id=$comanda_get");
            if ($CLD_CON->FetchArray()) {
                $tt = 0;
                $id_c = $CLD_CON->GetArrayField("id");
                $fecha = $CLD_CON->GetArrayField("fecha");
                $f_date = date("d M,Y", strtotime($fecha));
                $a_id = $CLD_CON->GetArrayField("address");
                $c_id = $CLD_CON->GetArrayField("contact");
                $impost_c = $CLD_CON->GetArrayField("impostos");
                $shop_c = $CLD_CON->GetArrayField("shop");
                $currency = $CLD_CON->GetArrayField("currency");

                $CLD_CON3->OpenRs("SELECT * FROM SHP_currency WHERE id=$currency");
                if ($CLD_CON3->FetchArray()) {
                    $currency_symbol = $CLD_CON3->GetArrayField("symbol_html");
                }

                $CLD_CON3->OpenRs("SELECT * FROM SHP_address WHERE id=$a_id");
                if ($CLD_CON3->FetchArray()) {
                    $address = "<div style='width:90%;text-align:right;border:2px solid gray;padding: 20px;'>";
                    $address .= "<p> " . $CLD_CON3->GetArrayField("Street") . ", " . $CLD_CON3->GetArrayField("Number") . "</p>";
                    $address .= "<p> (" . $CLD_CON3->GetArrayField("zip") . ")" . $CLD_CON3->GetArrayField("City") . "</p>";
                    $address .= "<p> " . $CLD_CON3->GetArrayField("State") . "</p></div>";
                }
                $CLD_CON3->OpenRs("SELECT * FROM SHP_Contacts WHERE id=$c_id");
                if ($CLD_CON3->FetchArray()) {
                    $contact = "<div style='width:90%;text-align:left;border:2px solid gray;margin-top:10px;padding: 20px;margin-bottom:50px;'>";
                    $contact.= "<p> " . $CLD_CON3->GetArrayField("Last_Name") . ", " . $CLD_CON3->GetArrayField("Name") . "</p>";
                    $contact .= "<p>" . $CLD_CON3->GetArrayField("Phone") . "</p><p> " . $CLD_CON3->GetArrayField("email") . "</p></div>";
                }

                echo "<div class='comanda' id='c$id_c' style='background-color:#FFA500;color:white;'>";
                echo "<p>Order $id_c <span style='display:inline;float:right;'>$f_date";
                echo "<img src='images/eye.png' style='width:24px;height:24px;cursor:pointer;' onclick='open_Close(\"$id_c\")'>";
                echo "<img src='images/print.png' style='width:24px;height:24px;cursor:pointer;' onclick='print0(\"$id_c\")'>";
                echo "</span></p>";
                echo "<div style='background-color:white;width:100%;height:auto;'>";
                echo "<div style='width:90%;background-color:white;color:black;margin-left: 5%;' id='con_add$id_c' class='order_info'>";
                echo $address;
                echo $contact;
                echo "</div>";
                $CLD_CON2->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE comanda=$id_c");
                echo "<table style='width:90%;margin-left:5%;background-color:white;' border='1' id='t$id_c' class='table_info'>";
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
                        $p_name = $CLD_CON3->GetArrayField("name");
                        $p_code = $CLD_CON3->GetArrayField("code");
                        $p_des = $CLD_CON3->GetArrayField("descripcio");
                        $style = $CLD_CON3->GetArrayField("style2");
                        $image = $_SERVER['DOCUMENT_ROOT'] . "/images/shop/$p_code/1.png";
                        $image = substr($image, strpos($image, "htdocs/") + 7);
                    }

                    echo "<tr style='border:1px solid black;'>";
                    echo "<td style='width:35%;border:1px solid black;'>";
                    echo "<div class='productMiniShop'>";
                    echo "<img class='imgProduct' src='../$image'>";
                    echo "<img src='https://myphotocode.com/$img' class='photoMini' style='$style'></a>";
                    echo "</div>";
                    echo "<div style='background-color: white;height: 250px;z-index: 2;width: 25%;float: right;display: inline;position: relative;'></div>";
                    $html2 .= "<tr style='padding:20px;margin:5px;'><td style='padding:20px;margin:5px;'> $p_code</td><td style='padding:20px;margin:5px;'> $p_name </td><td style='padding:20px;margin:5px;'>$qty</td><td style='padding:20px;margin:5px;'>$preu$currency_symbol</td><td style='padding:20px;margin:5px;'>" . ($qty * $preu) . "$currency_symbol</td></tr>";
                    echo "</td><td style='width:65%;vertical-align:top;'>";
                    echo "<p>$p_name</p>";
                    echo "<p>$p_des</p>";
                    echo "</td></tr>";
                    echo "<tr style='border:1px solid black;'><td></td><td><b>$preu$currency_symbol x $qty units</b><span style='margin-right:5%;float:right;display:inline;'>" . ($qty * $preu) . "$currency_symbol</td></tr>";
                    $tt = $tt + ($qty * $preu);
                }
                $imp = ($tt * $impost_c) / 100;
                echo "<tr style='border:1px solid black;'><td></td><td><b>TAX $impost_c%</b> <span style='margin-right:5%;float:right;display:inline;'>$imp$currency_symbol</td></tr>";
                echo "<tr style='border:1px solid black;'><td></td><td><b>TOTAL</b> <span style='margin-right:5%;float:right;display:inline;'>" . ($tt + $imp) . "$currency_symbol</td></tr>";
                echo "</table>";
                $html2 .= "<tr style='padding:20px;margin:5px;' ><td colspan='3' style='padding:20px;margin:5px;'></td><td style='padding:20px;margin:5px;'>TAX $impost_c% </td><td style='padding:20px;margin:5px;'>$imp$currency_symbol</td></tr>";
                $html2 .= "<tr style='padding:20px;margin:5px;'><td colspan='3' style='padding:20px;margin:5px;'></td><td style='padding:20px;margin:5px;'>TOTAL </td><td style='padding:20px;margin:5px;'>" . ($tt + $imp) . "$currency_symbol</td></tr>";
                $html2 .= "</table>";
                if (!file_exists("comandes/$id_c.pdf")) {
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
                    $output_file = "./comandes/$id_c.pdf";
                    $html2pdf->Output($output_file, 'F');
                }
                echo "<iframe src='./comandes/$id_c.pdf' width='0px' height='0px' id='iframe$id_c' style='border:0px solid transparent;'></iframe>";
                echo "</div>";
                echo "</div>";
            }else{
                echo "<p> Aquesta comanda no existeix</p>";
            }
            ?>


        </div>
        <script>
                        function print0(c) {
                var PDF = document.getElementById("iframe" + c);
                PDF.focus();
                PDF.contentWindow.print();
                /*$(".comanda").hide();
                 $(".titles").hide();
                 $("#t" + c).show();
                 $("#con_add" + c).show();
                 $("#c" + c).show();
                 print();
                 $(".comanda").show();
                 $(".titles").show();*/
                var ajaxData = {comanda: c};
                $.ajax({
                    url: 'setToPrinted.php',
                    method: 'POST',
                    success: function() {
                        get_comandes();
                    },
                    data: ajaxData
                });
                //print();
                
            }
        </script>
    </body>

</html>





