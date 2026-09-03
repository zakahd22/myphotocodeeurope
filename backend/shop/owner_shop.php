<?php
include '../conf.php';
include '../conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON4 = clone($CLD_CON);
?>
<div>
    <form method='POST' action='owner_shop'>
        <select name='mes'>
            <option value='0'>-----</option>
            <option value='1'>January</option>
            <option value='2'>February</option>
            <option value='3'>March</option>
            <option value='4'>April</option>
            <option value='5'>May</option>
            <option value='6'>June</option>
            <option value='7'>July</option>
            <option value='8'>August</option>
            <option value='9'>September</option>
            <option value='10'>October</option>
            <option value='11'>November</option>
            <option value='12'>December</option>
        </select>

        <select name='any'>
            <option value='0'>-----</option>
            <?php
            $pA = 2014;
            $A = date("Y");
            while ($pA <= $A) {
                echo "<option value='$pA'>$pA</option>";
                $pA++;
            }
            ?>
        </select>
        <input type='text' name='owner' placeholder="Owner Name">
        <input type='submit' name="search" value="Search">
    </form>
</div>

<?php
$txc_ow = "0.1";
$tt = 0;
$where = "";
$where2 = "";
if (isset($_POST['mes']) && isset($_POST['any'])) {
    if ($_POST['any'] != 0) {
        $mes = $_POST['mes'];
        $any = $_POST['any'];
        $any2 = $any;
        if($mes == 0){
            $mes = "01";
            $mes2 = "01";
            $any2 = $any + 1; 
        }else{
        if ($mes < 10) {
            $mes2 = $mes + 1;
            $mes = "0" . $mes;
            if ($mes2 < 10) {
                $mes2 = "0" . $mes2;
            }
        }else{
            $mes2 = $mes + 1;
        }
        if($mes2 == 13){
            $mes2 = "01";
            $any2 = $any +1;
        }
        }
        $fecha1 = $any . "-" . $mes . "-01 00:00:00";
        $fecha2 = $any2 . "-" . $mes2 . "-01 00:00:00";
        $where = "AND c.fecha BETWEEN '$fecha1' AND '$fecha2' ";
        echo "<p>$fecha1  to $fecha2</p>";
    }
}
if(isset($_POST['owner'])){
    if(!empty($_POST['owner'])){
        $ow = $_POST['owner'];
        $where2 = "WHERE name LIKE '%$ow%'";
    }
}


$CLD_CON->OpenRs("SELECT cp.photoCode FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON c.id=cp.comanda WHERE c.estat=1 $where GROUP BY cp.photoCode");
$in = "";
while ($CLD_CON->FetchArray()) {
    $in .= "'" . $CLD_CON->GetArrayField("photoCode") . "' , ";
}
$in .= " '0' ";
$CLD_CON3->OpenRs("SELECT id , name FROM rentals $where2");
while ($CLD_CON3->FetchArray()) {
    $rId = $CLD_CON3->GetArrayField("id");
    $rName = $CLD_CON3->GetArrayField("name");
    $CLD_CON->OpenRs("SELECT p.code FROM photos p LEFT JOIN events e ON e.id=p.event_id WHERE p.code IN ($in) AND e.rental_id =$rId");
    echo "<div style='width:22%;margin:1%;border:2px solid black;display:inline;float:left;'>";
    echo "<p style='padding:10px;background-color:blue;color:white;margin-top:0px;'> $rName </p>";
    while ($CLD_CON->FetchArray()) {
        echo "<table style='width:100%;font-weight:bold;'>";
        $ph = $CLD_CON->GetArrayField("code");
        $CLD_CON2->OpenRs("SELECT cp.preu , cp.qty , cp.producte  , c.fecha FROM SHP_Comandes_Products cp LEFT JOIN SHP_Comandes c ON c.id=cp.comanda WHERE c.estat=1 and cp.photoCode='$ph' $where");
        $t = 0;
        while ($CLD_CON2->FetchArray()) {
            $p = $CLD_CON2->GetArrayField("preu");
            $q = $CLD_CON2->GetArrayField("qty");
            $producte = $CLD_CON2->GetArrayField("producte");
            $fecha = $CLD_CON2->GetArrayField("fecha");
            $f = $p * $q;
            $f = number_format($f, 2);
            $CLD_CON4->OpenRs("SELECT name FROM SHP_products WHERE id=$producte");
            if ($CLD_CON4->FetchArray()) {
                $producte = $CLD_CON4->GetArrayField("name");
            }
            echo "<tr><td style='padding: 0px 10px;'>$fecha</td><td style='padding: 0px 20px;'>$producte</td><td style='padding:0px 20px;'>$p$</td><td style='padding:0px 20px;'>$q</td><td style='padding:0px 20px;'>" . $f . "$ </td></tr>";
            //echo "<p style='padding-left:10px;'> $fecha  ->  $producte  -> $p$ x $q  ->" . $p * $q . "$ </p>";
            $t = $t + $f;
        }
        $t = number_format($t, 2);
        echo "</table>";
        $to_owner = $t * $txc_ow;
        $to_owner = number_format($to_owner, 2);
        //echo "<tr><td colspan=4>TOTAL</td><td>$t$</td></tr>";
        echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;'> TOTAL <span style='float:right;'> $t$</span></p>";
        //echo "<tr><td colspan=4>TOTAL to Owner($txc_ow%)</td><td>" . ($t * $txc_ow) . "$</td></tr>";
        echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;'> TOTAL to Owner (". $txc_ow*100 ."%)  <span style='float:right;'> " . $to_owner . "$ </span></p>";

        $tt += $t;
    }

    if ($CLD_CON->GetRsRows() == 0) {
        echo "<p style='text-align:center;'>- No Comands -</p>";
    }
    echo "</div>";
}
$tt = number_format($tt, 2);
$tt_owner = $tt * $txc_ow;
$tt_owner = number_format($tt_owner, 2);
echo "<div style='width:95%;margin:1%;display:block;float:left;border:2px solid black;'>";
echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;text-align:center;'>Total</p>";
echo "<p style='padding:10px;'>Total : " . $tt . "$</p>";
echo "<p style='padding:10px;'>Total to Owners : " . $tt_owner . "$</p>";
echo "<p style='padding:10px;background-color:blue;color:white;margin:0px;text-align:center;'>Total</p>";
echo "</div>";
?>
