<?php
include 'conf.php';
include 'conexio.php';
?>

<style>
    body{
        overflow: hidden;
    }
    .miniShop{
        width: 60%;
        height: auto;
        border: 2px solid gray;
        border-radius: 20px;
        overflow: hidden;
        display: block;
        margin-left:20%;
        background-color: white;
        margin-bottom: 20px;
        margin-top: -85px;
    }
    .shop{
        position:absolute;
        width:15%;
        right:0%;
        top:10%;
        background-color: black;
    }

    .pestanyaShop{
        position:relative;
        left: -30px;
        top:20px;
        width:32px;
        height:80px;
        background-color: black;
        color: white;
        cursor:pointer;
        padding: 20px 0px;
        font-weight: bolder;
        font-size: 13pt;
        text-align: center;
        border-radius: 10px 0px 0px 10px;
    }
    .productMiniShop{
        position:relative;
        display:block;
        float:left;
        width: 100%;
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
<div class='shop' id='shopAll'>
    <div class='pestanyaShop' onClick="toggleShop();"> 
        S<br>H<br>O<br>P
    </div>
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
            echo "</div>";
        }
        ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
        var x = 0;
        
        $(document).ready(function(){
           setTimeout(function() {
                        toggleShop();
                    }, 2000);
        });
        
        function toggleShop() {
            if (x !== 3) {
                if (x === 0) {
                    x = 3;
                    closeShop();
                    setTimeout(function() {
                        x = 1;
                    }, 2000);
                }
                if (x === 1) {
                    x = 3;
                    openShop();
                    setTimeout(function() {
                        x = 0;
                    }, 2000);
                }
            }
       }
       
 function closeShop(){
    $("#shopAll").animate({
    right: "-15%"
  }, 2000);
 }      
 
  function openShop(){
    $("#shopAll").animate({
    right: "0"
  }, 2000);
 }    
</script>    
