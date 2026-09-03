<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$types = Array();
$CLD_CON->OpenRs("SELECT * FROM CLD_boothTypes ORDER BY CLD_modelSN");
while ($CLD_CON->FetchArray()) {
    $id = $CLD_CON->GetArrayField("id");
    $types[$id] = Array($CLD_CON->GetArrayField("name") . "", $CLD_CON->GetArrayField("CLD_modelSN") . "");
}

$status_totals = [0,0,0,0,0,0,0,0,0];

if ($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 2 || $_SESSION['USERTYPE']==6) {
    echo "<table class='tableStocks'>";
    echo "<tr><td colspan='12' class='black'>Totals - Tots</td></tr>";
    echo "<tr>";
    echo "<td class='black'></td>";
    $t = 0;
    while ($t < 9) {
        echo "<td class='black'>" . $BOOTHS_TYPE_STATUS[$t] . "</td>";
        $t++;
    }
    echo "<td class='black'>TOTAL STOCK</td>";
    echo "<td class='black'>TOTAL</td>";
    echo "</tr>";


    foreach ($types as $key => $value) {
        $t = 0;
        $tot = 0;
        $tot2 = 0;
        echo "<tr>";
        echo "<td STYLE='text-align:left;' class='black'>$value[1] - $value[0]</td>";
        while ($t < 9) {

            $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=$t AND CLD_idType=$key");
            $n = $CLD_CON->GetRsRows();
            echo "<td>$n</td>";
            $tot = $tot + $n;
            $status_totals[$t] =  $status_totals[$t] + $n;
            
            if ($t != 3 && $t > 1) {
                $tot2 = $tot2 + $n;
            }
            $t++;
        }
        echo "<td>$tot2</td>";
        echo "<td>$tot</td>";
        echo "</tr>";
    }
echo "<tr><td>TOTALS</td><td>$status_totals[0]</td><td>$status_totals[1]</td><td>$status_totals[2]</td><td>$status_totals[3]</td><td>$status_totals[4]</td><td>$status_totals[5]</td><td>$status_totals[6]</td><td>$status_totals[7]</td><td>$status_totals[8]</td></tr>";
    $status_totals = [0,0,0,0,0,0,0,0,0];
    
    
    echo "<tr><td colspan='12' class='black'>DC - Stocks</td></tr>";
    echo "<tr>";
    echo "<td class='black'></td>";
    $t = 0;
    while ($t < 9) {
        echo "<td class='black'>" . $BOOTHS_TYPE_STATUS[$t] . "</td>";
        $t++;
    }
    echo "<td class='black'>TOTAL STOCK</td>";
    echo "<td class='black'>TOTAL</td>";
    echo "</tr>";
    echo "</table>";
    echo "<h3>DC-Stocks</h3>";
    distributorStock(1, $CLD_CON , $BOOTHS_TYPE_STATUS , $types);
      echo "<h3>DCA-Stocks</h3>";
    distributorStock(2, $CLD_CON , $BOOTHS_TYPE_STATUS , $types);
        echo "<h3>MATT-Stocks</h3>";
    distributorStock(3, $CLD_CON , $BOOTHS_TYPE_STATUS , $types);
}


if ($_SESSION['USERTYPE'] == 3) {
    $dis = $_SESSION['USERID'];
    distributorStock($dis, $CLD_CON , $BOOTHS_TYPE_STATUS , $types);
    
}


function distributorStock($dis , $CLD_CON ,$BOOTHS_TYPE_STATUS , $types){
    echo "<table class='tableStocks'>";
    echo "<tr><td colspan='12' class='black'>Totals - Tots</td></tr>";
    echo "<tr>";
    echo "<td class='black'></td>";
    $t = 2;
    while ($t < 9) {
        if ($t != 3) {
            echo "<td class='black'>" . $BOOTHS_TYPE_STATUS[$t] . "</td>";
        }
        $t++;
    }

    echo "<td class='black'>TOTAL STOCK</td>";
    echo "<td class='black'>TOTAL SOLD</td>";
    echo "<td class='black'>TOTAL</td>";
    echo "</tr>";
    $tot3 = 0;
    $tottot=0;
    foreach ($types as $key => $value) {
        $t = 2;
        $tot = 0;
        $tot2 = 0;
        echo "<tr>";
        echo "<td STYLE='text-align:left;' class='black'>$value[1] - $value[0]</td>";
        while ($t < 9) {
            $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=$t AND CLD_idType=$key AND CLD_Distributor=$dis");
            $n = $CLD_CON->GetRsRows();
            $status_totals[$t] =  $status_totals[$t] + $n;
            if ($t == 3) {
                
                $sold = "<td>$n</td>";
            } else {
                $tot2 = $tot2 + $n;
                
                echo "<td>$n</td>";
            }
            $tot = $tot + $n;
            
            $t++;
        }
        $tot3 = $tot3 + $tot2;
        echo "<td>$tot2</td>";
        ECHO $sold;
        echo "<td>$tot</td>";
        $tottot = $tottot + $tot;
        echo "</tr>";
    }
echo "<tr style='background-color:#669933; color: white;'><td>TOTALS</td><td>$status_totals[2]</td><td>$status_totals[4]</td><td>$status_totals[5]</td><td>$status_totals[6]</td><td>$status_totals[7]</td><td>$status_totals[8]</td><td>$tot3</td><td>$status_totals[3]</td><td>$tottot</tr>";
  
    ECHO "</TABLE>";
} 
/*
  echo "<div class='inContent'>";
  if ($_SESSION['USERTYPE'] == 1 && $_SESSION['USERTYPE'] == 1) {

  //Producció

  echo "<h1>PhotoBooths Stocks</h1>";
  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Production PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=0");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=0 AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=0 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";
  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Finsih Product PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=1");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=1 AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=1 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>DC Stock PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=1");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=1  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=1 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>DCA Stock PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=2");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=2  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=2 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>MATT Stock PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=3");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=3  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=3 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>DC Sold PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=1");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=1  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=1 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>DCA Sold PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=2");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=2  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=2 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>MATT Sold PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=3");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=3  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=3 AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";
  }


  if ($_SESSION['USERTYPE'] == 3) {
  $dis = $_SESSION['USERID'];

  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Stock PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=2 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Sold PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=3 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Returned PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=4 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=4 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=4 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Returned & Damaged PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=5 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=5 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=5 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Damaged PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=6 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=6 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=6 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Incomplete PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=7 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=7 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=7 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";


  echo "<div style='text-align:center;width:19%;border-right:2px solid gray;display:inline;float:left;'>";
  echo "<h3>Refurbished PhotoBooths</h3>";
  echo "<table style='width:90%;margin-left:5%;'>";
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=8 AND CLD_Distributor=$dis");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Total : </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  foreach ($types as $key => $value) {
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=8 AND CLD_Distributor=$dis  AND CLD_idType=$key");
  echo "<tr><td style='border:1px solid black;padding:2px;'> $value[1] -> $value[0] </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";
  }
  $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=8 AND CLD_Distributor=$dis AND CLD_idType IS NULL ");
  echo "<tr><td style='border:1px solid black;padding:2px;'> Uknows </td><td style='border:1px solid black;padding:2px;'> " . $CLD_CON->GetRsRows() . "</td></tr>";

  echo "</table>";
  echo "</div>";
  }
  echo "</div>"; */
?>
