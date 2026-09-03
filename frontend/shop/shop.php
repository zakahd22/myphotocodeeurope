<?php
require_once '../common/global.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
?>
<html>
    <head>
        <?php include 'head.php'; ?>
        <script type="text/javascript">
            function setColor(nCode, info) {
                $("#color").val(nCode);
                $(".color-box").removeClass("yellowBorder");
                $("#" + nCode).addClass("yellowBorder");
            }
            function setPreu(p, c) {
                var q = $("#qty").val();
                var preu_t = p * q;
                preu_t = parseFloat(preu_t + "").toFixed(2);
                $("#PreuxQty").html("Price : " + preu_t + c);

            }
        </script>


    </head>
    <body>
        <?php
        include 'header.php';
        $id = $_GET['p'];
        $code = $_GET['code'];
        $photo = $_GET['pho'];

        $_SESSION['product_id'] = $id;
        $_SESSION['product_code'] = $code;
        $_SESSION['photo'] = $photo;

        echo "<div class='blok blokWhite'>";
        $CLD_CON->OpenRs("SELECT * FROM SHP_products WHERE code='$code' AND id=$id");


        if ($CLD_CON->FetchArray()) {
            echo "<form action='./getAddress.php' method='POST'>";
            $nom = $CLD_CON->GetArrayField("name");
            $descripcio = $CLD_CON->GetArrayField("descripcio");
            $preu = $CLD_CON->GetArrayField("preu");
            $shop = $CLD_CON->GetArrayField("shop");
            $styles_p = $CLD_CON->GetArrayField("style");
            $image = "../images/shop/$code/1.png";
            echo "<style>$styles_p</style>";
            $CLD_CON2->OpenRs("SELECT c.symbol_html , c.symbol  FROM SHP_Shops s LEFT JOIN SHP_currency c ON s.currency = c.id WHERE s.id=$shop");
            if ($CLD_CON2->FetchArray()) {
                $currency_html = $CLD_CON2->GetArrayField("symbol_html");
            }


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



            echo "<div class='contentDiv'>";
            echo "<input type='hidden' name='productID'  value='$id'>";
            echo "<input type='hidden' name='productCode'  value='$code'>";
            echo "<input type='hidden' name='codeIMG' value=''>";
            echo "<input type='hidden' name='productIMG' value='$image'>";
            $CLD_CON2->OpenRs("SELECT * FROM SHP_caracteristiques WHERE producte=$id");
            while ($CLD_CON2->FetchArray()) {
                $option_id = $CLD_CON2->GetArrayField("id");
                $option_name = $CLD_CON2->GetArrayField("nom");
                $option_style = $CLD_CON2->GetArrayField("style");
                $option_code = $CLD_CON2->GetArrayField("code");
                getOptions($option_id, $option_name, $option_style, $option_code);
            }
            $nn = 1;
            echo "<h2>$nom </h2>";
            echo "<span> Quantity : ";
            echo '<div class="styled-select">';
            echo "<select id='qty' name='qty' onchange='setPreu($preu , \"$currency_html\")' class='sel'>";
            while ($nn < 101) {
                echo "<option value='$nn'>$nn</option>";
                $nn++;
            }
            echo "</select></div></span>";
            echo "<p id='PreuxQty'> Price : $preu$currency_html</p>";

            echo "$descripcio";
            echo "<div style='text-align: right;width: 108%;position: relative;margin-top: 85px;'>";
                echo "<img src='images/next.png' style='position: absolute;right: 10;'>";
                echo "<p style='font-size: 20pt;transform: rotateZ(27DEG);color: black;background-color: white;width: 193px;margin-right: -60px;border-top: 2px solid;border-bottom: 2px solid;position: absolute;top: -23px;right: 40px;text-align: center;'>Coming Soon</p>";
            echo "</div>";
            //echo "<input type='submit' name='sub' value ='Next' class='nextButton'>";
            echo "</div>";
            echo "</form>";
        } else {
            echo "<p>I'm sorry but this product don't exist</p>";
        }

       
 /**More Accesories*/
echo "<div style='width:80%;display:inline;float:left;margin-left:10%;'>";
        


                $CLD_CON->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.code='$photo'");
                if ($CLD_CON->FetchArray()) {
                    $date_e = $CLD_CON->GetArrayField("start_date");
                    $id_e = $CLD_CON->GetArrayField("id");
                    $img = "/events/$date_e$id_e/$photo.jpg";
                    $img_url = $_SERVER['DOCUMENT_ROOT'] . "/events/$date_e$id_e/$photo.jpg";
                }

                list($width, $height) = getimagesize($img_url);

                if ($height > $width) {
                    $where = "AND stripV = 1";
                    $more = false;
                } else {
                    if ($height > 1000) {
                        $more = true;
                        $where = "AND p1015 = 1";
                    } else {
                        $where = "";
                        $more=false;
                    }
                }
                if($more){
                echo "<h2>More accessories</h2>";
                $CLD_CON->OpenRs("SELECT * FROM SHP_products WHERE shop = $shop $where AND id != $id");
                while ($CLD_CON->FetchArray()) {
                    $name_p = $CLD_CON->GetArrayField("name");
                    $code_p = $CLD_CON->GetArrayField("code");
                    $id_p = $CLD_CON->GetArrayField("id");
                    $preu_p = $CLD_CON->GetArrayField("preu");
                    $descripcio = substr($CLD_CON->GetArrayField("descripcio"), 0, 25) . "...";
                    $style2 = $CLD_CON->GetArrayField("style2");
                    $image = "images/shop/$code_p/1.png";

                    echo "<div class='productMiniShop'>";
                    echo "<a href='./shop.php?p=$id_p&code=$code_p&pho=$photo' style='z-index: 10;'><img class='imgProduct' src='$image'>";
                    echo "<img src='$img' class='photoMini' style='$style2'></a>";
                    echo "</div>";
                    echo "<div style='position:relative; display:inline; float:left; width:7%;background-color:white;height:55%;z-index: 5; '></div>";
                }
                }
                 echo "</div>";
                  echo "</div>";
                ?>
    </body>

    <?php include 'footer.php'; ?>
</html>


<?php

function getOptions($i, $n, $s, $c) {
    global $CLD_CON3;
    $CLD_CON3->OpenRs("SELECT * FROM SHP_ch_options WHERE caract=$i ORDER BY id");
    switch ($s) {
        case 1: // Desplegable 
            echo "<p> <b>$n </b>: <select name='$c'>";
            while ($CLD_CON3->FetchArray()) {
                $code_op = $CLD_CON3->GetArrayField("code");
                $name_op = $CLD_CON3->GetArrayField("name");
                echo "<option value='$code_op'>$name_op</option>";
            }
            echo "</select></p>";
            break;
        case 2:
            $x = 0;
            echo "<p> <b>$n </b>:</p>";
            echo "<div style='width:100%;margin-bottom:3px;overflow:hidden;'>";
            while ($CLD_CON3->FetchArray()) {

                $code_op = $CLD_CON3->GetArrayField("code");
                $name_op = $CLD_CON3->GetArrayField("name");
                $info_op = $CLD_CON3->GetArrayField("info");
                if ($x == 0) {
                    echo "<input type='hidden' name='$c' id='color' value='WHT'>";
                    echo "<div class='color-box yellowBorder' style='background-color:$info_op;' title='$name_op' onclick='setColor(\"$code_op\" , \"$info_op\")' id='$code_op'></div>";
                } else {
                    echo "<div class='color-box' style='background-color:$info_op;' title='$name_op' onclick='setColor(\"$code_op\" , \"$info_op\")' id='$code_op'></div>";
                }
                $x++;
            }
            echo "</div>";
            break;
    }
}
?>


