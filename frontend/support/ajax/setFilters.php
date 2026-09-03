<?php

include '../sessio.php';
include '../conexio.php';

$boothType = $_POST['boothType'];
$filters = Array();
$i = 0;
$x = 0;
$c = true;
if ($_SESSION['USERID'] == 9999991) {
    $owner = $_POST['owner'];
    if ($owner != 0) {
        $filters[$i] = "b.owner = $owner";
        $i++;
    }
    if ($boothType != '0') {
        $filters[$i] = "b.type = '$boothType'";
        $i++;
    }
} else {
    $filters[$i] = "b.owner =" . $_SESSION['USERID'];
    $i++;
    if ($boothType != '0') {
        $filters[$i] = "b.type = '$boothType'";
        $i++;
    }
}
$where = "";
while ($x < $i) {
    if ($c) {
        $where .= " WHERE ";
        $c = false;
    }
    $where .= $filters[$x];
    $x++;
    if ($x < $i) {
        $where .= " AND ";
    }
}
echo "<option value='0' selected>--- None ---</option>";
$CLD_CON->OpenRs("SELECT b.name bName , bt.name btName , b.idBooth FROM App_booths b LEFT JOIN booth_types bt  ON b.type = bt.char $where ORDER BY  bt.name , b.name");
while ($CLD_CON->FetchArray()) {
    $boothId = $CLD_CON->GetArrayField("idBooth");
    $boothName = $CLD_CON->GetArrayField("bName");
    $typeName = $CLD_CON->GetArrayField("btName");
    echo "<option value='$boothId'><span class='text'> $typeName - $boothName </span></option>";
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
