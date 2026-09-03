<?php
include '../conf.php';
include '../conexio.php';
$shop = $_GET['s'];
$comanda = $_GET['c'];
?>
<html>
    <head>
        <?php include 'head.php'; ?>
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&language=en"></script>
    </head>
    <body>
        <?php
        include 'header.php';
        ?>
        <div class='blok blokWhite'>  
            <div class='imgContainer' style='border: 0px;'>
                <?php echo "<img  src='images/starts.png' style='width:80%;margin-left:10%;margin-top:2%;'>"; ?>
            </div>
            <div  class='contentDiv'>
                <h2> Thank you very much!!</h2>
                <p>Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas "Letraset", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.</p>
            </div>
            <div style='width:80%;display:inline;float:left;margin-left:10%;margin-top:30px;'>
                <?php
                $CLD_CON->OpenRs("SELECT photoCode FROM SHP_Comandes_Products WHERE comanda=$comanda");
                if ($CLD_CON->FetchArray()) {
                    $photo = $CLD_CON->GetArrayField("photoCode");
                }

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
                } else {
                    if ($height > 1000) {
                        $where = "AND WHERE p1015 = 1";
                    } else {
                        $where = "";
                    }
                }

                echo "<h2> Mas Productos con la foto $photo</h2>";
                $CLD_CON->OpenRs("SELECT * FROM SHP_products WHERE shop = $shop $where");
                while ($CLD_CON->FetchArray()) {
                    $name_p = $CLD_CON->GetArrayField("name");
                    $code_p = $CLD_CON->GetArrayField("code");
                    $id_p = $CLD_CON->GetArrayField("id");
                    $preu_p = $CLD_CON->GetArrayField("preu");
                    $descripcio = substr($CLD_CON->GetArrayField("descripcio"), 0, 25) . "...";
                    $style2 = $CLD_CON->GetArrayField("style2");
                    $image = "https://myphotocode.com/images/shop/$code_p/1.png";

                    echo "<div class='productMiniShop'>";
                    echo "<a href='./shop.php?p=$id_p&code=$code_p&pho=$photo' style='z-index: 10;'><img class='imgProduct' src='$image'>";
                    echo "<img src='https://myphotocode.com/$img' class='photoMini' style='$style2'></a>";
                    echo "</div>";
                }
                ?>

            </div>
        </div>
    </body>
    <?php include 'footer.php'; ?>
</html>