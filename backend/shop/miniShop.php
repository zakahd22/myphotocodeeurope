<?php
include 'conf.php';
include 'conexio.php';
?>
<style>
    .miniShop{
        max-width: 40%;
        height: auto;
        border: 2px solid gray;
        border-radius: 20px;
        padding: 2%;
        overflow: hidden;
        display: block;
    }
    .productMiniShop{
        position:relative;
        display:inline;
        float:left;
        width:32%;
        margin: 0 auto;
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
</style>
<div class='miniShop'>
    <?php
    $photo = "A9SXV3C2UJ";
    //$photo = "H2ELHQ5UT7";
    //$photo = "AMEG23596X";

    $CLD_CON->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.code='$photo'");
    if ($CLD_CON->FetchArray()) {
        $date_e = $CLD_CON->GetArrayField("start_date");
        $id_e = $CLD_CON->GetArrayField("id");
        $img = "/events/$date_e$id_e/$photo.jpg";
        $img_url = $_SERVER['DOCUMENT_ROOT'] . "/events/$date_e$id_e/$photo.jpg";
    }

    list($width, $height) = getimagesize($img_url);

    if ($height > $width) {
        $where = "WHERE stripV = 1";
    } else {
        if ($height > 1000) {
            $where = "WHERE p1015 = 1";
        } else {
            $where = "";
        }
    }


    $CLD_CON->OpenRs("SELECT * FROM SHP_products $where");
    while ($CLD_CON->FetchArray()) {
        $id = $CLD_CON->GetArrayField("id");
        $code = $CLD_CON->GetArrayField("code");
        $nom = $CLD_CON->GetArrayField("name");
        $descripcio = substr($CLD_CON->GetArrayField("descripcio"), 0, 25) . "...";
        $preu = $CLD_CON->GetArrayField("preu");
        $style2 = $CLD_CON->GetArrayField("style2");

        $image = $_SERVER['DOCUMENT_ROOT'] . "/images/shop/$code/1.png";
        $image = substr($image, strpos($image, "htdocs/") + 7);
        echo "<div class='productMiniShop'>";
        echo "<a href='./shop/shop.php?p=$id&code=$code&pho=$photo' target='_blank' style='z-index: 10;'><img class='imgProduct' src='$image'>";
        echo "<img src='https://myphotocode.com/$img' class='photoMini' style='$style2'></a>";
        echo "<p> <b>$nom</b> - $descripcio </p>";
        echo "</div>";
    }
    ?>
</div>

