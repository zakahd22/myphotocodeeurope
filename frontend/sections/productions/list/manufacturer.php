<?php
$CLD_CON2 = clone($CLD_CON);
echo "<h1>Productions <input type='button' class='miniAdd' onclick='edit(63 , 0)' title='add new production'></h1>";
$CLD_CON->OpenRs("SELECT * FROM CLD_Productions ORDER by  status ,startDate");
while ($CLD_CON->FetchArray()) {
    $idproduction = $CLD_CON->GetArrayField("id");
    $boothTypeName = $CLD_CON->GetArrayField("boothType");
    $numMachines = $CLD_CON->GetArrayField("num_products");
    $startDate = $CLD_CON->GetArrayField("startDate");
    $endDate = $CLD_CON->GetArrayField("endDate");
    $status = $CLD_CON->GetArrayField("status");
    $idtype = 0;
    $CLD_CON2->OpenRs("SELECT id FROM CLD_boothTypes WHERE name='$boothTypeName'");
    if ($CLD_CON2->FetchArray()) {
        $idtype = $CLD_CON2->GetArrayField("id");
    }
    $CLD_CON2->OpenRs("SELECT serialnumber FROM App_booths WHERE CLD_production=$idproduction ORDER BY serialnumber LIMIT 1");
    if ($CLD_CON2->FetchArray()) {
        $sn1 = $CLD_CON2->GetArrayField("serialnumber");
    }
    $CLD_CON2->OpenRs("SELECT serialnumber FROM App_booths WHERE CLD_production=$idproduction ORDER BY serialnumber DESC LIMIT 1");
    if ($CLD_CON2->FetchArray()) {
        $sn2 = $CLD_CON2->GetArrayField("serialnumber");
    }

    echo "<div style='width:80%;margin-left:10%;border:1px solid gray;overflow:hidden;height:33%;' onclick='profile(\"productions\" , \"PhotoBooths\" , $idproduction);'>";
        echo "<div style='width:50%;display:inline;float:left;text-align:center;'>";
            echo "<img src='images/web/pb/$idtype.png' style='height:90%;margin-top:3%;'>";
        echo "</div>";
        echo "<div style='width:49%;display:inline;float:left;'>";
            echo "<h1> Production of $numMachines $boothTypeName</h1>";
            echo "<p> $sn1 until $sn2</p>"; 
            $d1 = date("F d, Y", strtotime($startDate));
            echo "<p> Start date : " . $d1 . "</p>";
            if($status==0){
                echo "<p> In production </p>";
            }else{
                $d2 = date("F d, Y", strtotime($endDate));
                echo "<p> Finished - ".$d2." </p>";
            }
        echo "</div>";
    echo "</div>";
}
?>
