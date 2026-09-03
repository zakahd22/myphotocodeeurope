<?php

include '../conf.php';
include '../conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$txc_ow = "0.1";

$tt = 0;
$CLD_CON3->OpenRs("SELECT s.id , c.symbol_html FROM SHP_Shops s LEFT JOIN SHP_currency c ON c.id=s.currency");
while ($CLD_CON3->FetchArray()) {
    $sId = $CLD_CON3->GetArrayField("id");
    $sCurrency = $CLD_CON3->GetArrayField("symbol_html");
    echo "<div style='width:22%;margin:1%;border:2px solid black;display:inline;float:left;'>";
    echo "<p style='padding:10px;background-color:blue;color:white;margin-top:0px;'> Shop $sId </p>";
    $CLD_CON->OpenRs("SELECT cp.photoCode FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON c.id=cp.comanda WHERE c.estat=1 AND c.shop=$sId GROUP BY cp.photoCode");
    while ($CLD_CON->FetchArray()) {
        $ph = $CLD_CON->GetArrayField("photoCode");
        $CLD_CON2->OpenRs("SELECT cp.preu , cp.qty , cp.producte  , c.fecha FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON c.id=cp.comanda WHERE c.estat=1 and cp.photoCode='$ph'");
        $t = 0;
        while ($CLD_CON2->FetchArray()) {
            $p = $CLD_CON2->GetArrayField("preu");
            $q = $CLD_CON2->GetArrayField("qty");
            $producte = $CLD_CON2->GetArrayField("producte");
            $fecha = $CLD_CON2->GetArrayField("fecha");
            $f = $p * $q;
            $f = number_format($f , 2);
            echo "<p style='padding-left:10px;'> $fecha  ->  $producte  -> $p$ x $q  ->" . $f. "$sCurrency</p>";
            $t = $t + ($p + $q);
            $t = number_format($t , 2);
        }
        $dineroShop = $t * $txc_ow;
        $dineroShop  = number_format($dineroShop  , 2);
        echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;'> TOTAL :  $t$sCurrency</p>";
        echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;'> % of TOTAL ($txc_ow%)  : " . $dineroShop  . "$sCurrency</p>";
        $tt += $t;
        $tt = number_format($tt , 2);
    }
    if ($CLD_CON->GetRsRows() == 0) {
        echo "<p style='text-align:center;'>- No Comands -</p>";
    }
    echo "</div>";
}
$pcD = $tt * $txc_ow;
$pcD = number_format($pcD , 2);
    echo "<div style='width:95%;margin:1%;display:block;float:left;border:2px solid black;'>";
    echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;text-align:center;'>Total</p>";
    echo "<p style='padding:10px;'>TOTAL : $tt$sCurrency</p>";
    echo "<p style='padding:10px;'>% of TOTAL : " . $pcD . "$sCurrency</p>";
    echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;text-align:center;'>Total</p>";
    echo "</div>";
?>