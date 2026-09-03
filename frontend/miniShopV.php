<div class='shop' id='shopAll'>
    
    <p id='shopText'  style='color: white;text-shadow: 0px 0px 2px black;font-size: 30pt;margin-bottom: -115px;' >Buy a trendy accessory with your photo</p>
    <div class='pestanyaShop' onClick="toggleShop();"> 
        S<br>H<br>O<br>P
    </div>
  
    <div class='miniShop'>
        
        <?php


        $CLD_CON->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.code='$code'");
        if ($CLD_CON->FetchArray()) {
            $date_e = $CLD_CON->GetArrayField("start_date");
            $id_e = $CLD_CON->GetArrayField("id");
            $img = "/events/$date_e$id_e/$code.jpg";
            $img_url = $_SERVER['DOCUMENT_ROOT'] . "/events/$date_e$id_e/$code.jpg";
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
        if($CLD_CON->GetRsRows() > 1){
            echo "<marquee direction='up' style='height: 500px;' onmouseover='this.stop();' onmouseout='this.start();'>";
        }
        while ($CLD_CON->FetchArray()) {
            $id = $CLD_CON->GetArrayField("id");
            $code2 = $CLD_CON->GetArrayField("code");
            //$nom = $CLD_CON->GetArrayField("name");
           // $descripcio = substr($CLD_CON->GetArrayField("descripcio"), 0, 25) . "...";
           // $preu = $CLD_CON->GetArrayField("preu");
            $style2 = $CLD_CON->GetArrayField("style2");

            $image = $_SERVER['DOCUMENT_ROOT'] . "/images/shop/$code2/1.png";
            $image = substr($image, strpos($image, "htdocs/") + 7);
            echo "<div class='productMiniShop'>";
            echo "<a href='./shop/shop.php?p=$id&code=$code2&pho=$code' target='_blank' style='z-index: 10;'><img class='imgProduct' src='$image'>";
            echo "<img src='https://myphotocode.com/$img' class='photoMini' style='$style2'></a>";
            echo "</div>";
        }
                if($CLD_CON->GetRsRows() > 1){
            echo "</marquee>";
        }
        ?>
    </div>
</div>
<script>
                        var x = 0;

                        $(document).ready(function() {
                            setTimeout(function() {
                                toggleShop();
                            }, 4000);
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

                        function closeShop() {
                            $("#shopText").hide();
                            $("#shopAll").animate({
                                width: "0"
                            }, 2000);
                            
                        }

                        function openShop() {
                            $("#shopAll").animate({
                                width: "30%"
                            }, 2000);
                             $("#shopText").show();
                        }
                    </script> 
    
