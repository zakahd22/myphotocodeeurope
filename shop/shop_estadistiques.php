<?php

include '../conf.php';
include '../conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
echo "<html><head><style>.taza{width:101%;position: relative;    z-index: 3;    margin-top: 3%;}</style><head><body>";
$CLD_CON->OpenRs("SELECT * FROM SHP_products");
while ($CLD_CON->FetchArray()) {
    $code = $CLD_CON->GetArrayField("code");
    $image = "https://myphotocode.com/images/shop/$code/default.png";

    $name = $CLD_CON->GetArrayField("name");
    $descripcio = $CLD_CON->GetArrayField("descripcio");


    /* Shop i Currency */
    $shop = $CLD_CON->GetArrayField("shop");
    $CLD_CON2->OpenRs("SELECT * FROM SHP_Shops WHERE id=$shop");
    if ($CLD_CON2->FetchArray()) {
        $currency_id = $CLD_CON2->GetArrayField("currency");
        $CLD_CON3->OpenRs("SELECT * FROM SHP_currency WHERE id=$currency_id");
        if ($CLD_CON3->FetchArray()) {
            $currency_html = $CLD_CON3->GetArrayField("symbol_html");
        }
    }
    /* Total de visites */
    $producte_id = $CLD_CON->GetArrayField("id");
    $CLD_CON2->OpenRs("SELECT * FROM SHP_Comandes_Products WHERE producte = $producte_id");
    $total_visites = $CLD_CON2->GetRsRows();

    /* Visites el ultim any */
    $d1 = date("Y") . "-01-01";
    $d2 = date("Y-m-d");
    $CLD_CON2->OpenRs("SELECT cp.* FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.fecha BETWEEN '$d1' AND '$d2 23:59:00'");
    $visites_ultim_any = $CLD_CON2->GetRsRows();

    /* Visites el ultim mes */
    $d3 = date("Y-m") . "-01";
    $CLD_CON2->OpenRs("SELECT cp.* FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.fecha BETWEEN '$d3' AND '$d2 23:59:00'");
    $visites_ultim_mes = $CLD_CON2->GetRsRows();
    $d3 = date("Y-m") . "-01";
    $CLD_CON2->OpenRs("SELECT cp.* FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.fecha LIKE '$d2%'");
    $visites_hoy = $CLD_CON2->GetRsRows();

    /* Total Vengudes */
    $CLD_CON2->OpenRs("SELECT SUM(cp.qty) as sss  FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5");
    if ($CLD_CON2->FetchArray()) {
        $total_comprades = $CLD_CON2->GetArrayField("sss");
        if (empty($total_comprades)) {
            $total_comprades = 0;
        }
    }
    $CLD_CON2->OpenRs("SELECT SUM(cp.qty) as sss FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5 AND c.fecha BETWEEN '$d1' AND '$d2 23:59:00'");
    if ($CLD_CON2->FetchArray()) {
        $ultim_any_comprades = $CLD_CON2->GetArrayField("sss");
        if (empty($ultim_any_comprades)) {
            $ultim_any_comprades = 0;
        }
    }
    $CLD_CON2->OpenRs("SELECT SUM(cp.qty) as sss FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5 AND c.fecha BETWEEN '$d3' AND '$d2 23:59:00'");
    if ($CLD_CON2->FetchArray()) {
        $ultim_mes_comprades = $CLD_CON2->GetArrayField("sss");
        if (empty($ultim_mes_comprades)) {
            $ultim_mes_comprades = 0;
        }
    }
    $CLD_CON2->OpenRs("SELECT SUM(cp.qty) as sss FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5 AND c.fecha LIKE '$d2%'");
    if ($CLD_CON2->FetchArray()) {
        $hoy_comprades = $CLD_CON2->GetArrayField("sss");
        if (empty($hoy_comprades)) {
            $hoy_comprades = 0;
        }
    }

    /* Total Vengudes */
    $CLD_CON2->OpenRs("SELECT (cp.qty * cp.preu) as sss  FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5");
    $total_comprades_suma = 0;
    if ($CLD_CON2->FetchArray()) {
        $total_comprades_suma += $CLD_CON2->GetArrayField("sss");
    }
    $CLD_CON2->OpenRs("SELECT (cp.qty * cp.preu) as sss FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5 AND c.fecha BETWEEN '$d1' AND '$d2 23:59:00'");
    $ultim_any_comprades_suma = 0;
    if ($CLD_CON2->FetchArray()) {
        $ultim_any_comprades_suma += $CLD_CON2->GetArrayField("sss");
    }
    $CLD_CON2->OpenRs("SELECT (cp.qty * cp.preu) as sss FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5 AND c.fecha BETWEEN '$d3' AND '$d2 23:59:00'");
    $ultim_mes_comprades_suma = 0;
    if ($CLD_CON2->FetchArray()) {
        $ultim_mes_comprades_suma += $CLD_CON2->GetArrayField("sss");
    }
    $hoy_comprades_suma = 0;
    $CLD_CON2->OpenRs("SELECT (cp.qty * cp.preu) as sss FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON cp.comanda=c.id WHERE cp.producte = $producte_id AND c.estat=5 AND c.fecha LIKE '$d2%'");
    if ($CLD_CON2->FetchArray()) {
        $hoy_comprades_suma += $CLD_CON2->GetArrayField("sss");
    }

    echo "<div style='width:42%;display:inline; float:left;height:50%;'>";
    /* Imatge del product */
    echo "<div style='width:48%;display:inline;float:left;height:100%;'>";
    echo "<img  src='$image' class='taza'>";
    echo "</div>";
    /** Informació producte */
    echo "<div style='width:48%;display:inline;float:left;height:100%;padding-left:2%;'>";
    echo "<h2>Visits</h2>";
    echo "<p>All time : $total_visites visits </p>";
    echo "<p>Between $d1 and $d2 : $visites_ultim_any visits</p>";
    echo "<p>Between $d3 and $d2 : $visites_ultim_mes visits</p>";
    echo "<p>Today : $visites_hoy</p>";
    echo "<h2>Sales</h2>";
    echo "<p>All time : $total_comprades sales ($total_comprades_suma$currency_html)</p>";
    echo "<p>Between $d1 and $d2: $ultim_any_comprades sales ($ultim_any_comprades_suma$currency_html) </p>";
    echo "<p>Between $d1 and $d3: $ultim_mes_comprades sales ($ultim_mes_comprades_suma$currency_html) </p>";
    echo "<p>Today : $hoy_comprades ($hoy_comprades_suma$currency_html)</p>";
    echo "</div>";
    echo "</div>";
}
echo "</body></html>";
?>
