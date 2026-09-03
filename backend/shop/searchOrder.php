<?php
include '../conf.php';
include '../conexio.php';

$order= $_POST['orderID'];
$cmfCode = $_POST['cmfCode'];

$CLD_CON2 = clone($CLD_CON);

$CLD_CON3 = clone($CLD_CON);
$CLD_CON2->OpenRs("SELECT * FROM SHP_Comandes WHERE id=$order");
if($CLD_CON2->FetchArray()){
    
        $fecha = $CLD_CON2->GetArrayField("fecha");
        $printed = $CLD_CON2->GetArrayField("printed");
        
        $CLD_CON3->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE comanda=$order ");
        if($CLD_CON3->FetchArray()){
            $photo = $CLD_CON3->GetArrayField("photoCode");
            $id = $CLD_CON3->GetArrayField("producte");
   
        $CLD_CON->OpenRs("SELECT * FROM SHP_products WHERE id=$id");


        if ($CLD_CON->FetchArray()) {
            echo "<form action='./getAddress.php' method='POST'>";
            $nom = $CLD_CON->GetArrayField("name");
            $code = $CLD_CON->GetArrayField("code");
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
            $fecha = date("Ymd", strtotime($fecha));
            $pdfFile = "./comandes/$shop/$fecha/$order/$order.pdf";
            echo "<iframe id='iframepdf' src='$pdfFile' style='width:90%; margin-left:5%; height:500px;margin-top:5%;'></iframe>";
            echo "</div>";
        }
        
        }else{
            echo "<p>The order $order and Comfirmation code don&#39;t much with any order</p>";
        }
}else{
    echo "<p>The order $order not exist</p>";
}

?>
