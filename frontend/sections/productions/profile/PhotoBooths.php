<?php
include "../../../sessio.php";
require_once G_PATH . 'common/conexio.php'; 

$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT * FROM App_booths WHERE CLD_production=$ID ORDER BY serialnumber");


while ($CLD_CON->FetchArray()) {
    $idBooth = $CLD_CON->GetArrayField("idBooth");
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $char = $CLD_CON->GetArrayField("type");
    $typeID = $CLD_CON->GetArrayField("CLD_idType");
    $location = $CLD_CON->GetArrayField("location");
    $owner = $CLD_CON->GetArrayField("owner");
    $status = $CLD_CON->GetArrayField("CLD_Status");
    if (!empty($typeID)) {
        $CLD_CON2->OpenRs("SELECT name FROM CLD_boothTypes WHERE id=$typeID");
        if($CLD_CON2->FetchArray()){
            $typeName = $CLD_CON2->GetArrayField("name");
        }
    }else{
        $CLD_CON2->OpenRs("SELECT id , b.name FROM CLD_boothTypes WHERE b.char=$char LIMIT 1");
        if($CLD_CON2->FetchArray()){
            $typeName = $CLD_CON2->GetArrayField("name");
            $typeID = $CLD_CON2->GetArrayField("id");
        }
    }
    
    $CLD_CON2->OpenRs("SELECT idDongle FROM App_boothDongle WHERE idBooth=$idBooth AND datetimeF IS NULL LIMIT 1");
    if($CLD_CON2->FetchArray()){
        $dongleID = $CLD_CON2->GetArrayField("idDongle");
        $CLD_CON3->OpenRs("SELECT rand_string FROM booths WHERE id=$dongleID");
        if($CLD_CON3->FetchArray()){
            $r_string = " - " . $char . $CLD_CON3->GetArrayField("rand_string");
        }
    }else{
        $r_string  = "";
    }
    
    $Distributorid = $CLD_CON->GetArrayField("CLD_Distributor");
    $CLD_CON2->OpenRs("SELECT Name FROM CLD_Distributors WHERE id=$Distributorid");
    if($CLD_CON2->FetchArray()){
        $distributor = $CLD_CON2->GetArrayField("Name");
    }else{
        $distributor = "Undefined";
    }
    
    $CLD_CON2->OpenRs("SELECT name FROM rentals WHERE id=$owner");
    if($CLD_CON2->FetchArray()){
        $owner = $CLD_CON2->GetArrayField("name");
    }else{
        $owner = "Undefined";
    }
    
    
    echo "<div class='regBooth' onclick='setSection(\"photobooths\" ,2 ,$idBooth)' style='height:38%;'>";
    echo "<div class='imgListBooth'>";
    echo "<img src='images/web/pb/$typeID.png' style='width:80%;margin-left:10%;margin-top:10%;max-height:95%;'>";
    echo "</div>";
    echo "<div class='infoListBooth'>";
    echo "<p>S/N : $sn $r_string</p>";
    echo "<p>Type : $typeName</p>";
    echo "<p>Location : $location</p>";
    if($status==0){
        echo "<p>Status: $BOOTHS_TYPE_STATUS[0]</p>";
    }
     if($status==1){
        echo "<p>Status: $BOOTHS_TYPE_STATUS[1]</p>";
    }
     if($status==2){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[2]</p>";
    }
    if($status==3){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[3]</p>";
        echo "<p>Owner : $owner</p>";
    }   
    if($status==4){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[4]</p>";
    }   
    if($status==5){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[5]</p>";
    }  
      if($status==6){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[6]</p>";
    }   
    if($status==5){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[7]</p>";
    }  
    if($status==8){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[8]</p>";
    }   
    $CLD_CON2->OpenRs("SELECT * FROM CLD_Incidents WHERE idBooth=$idBooth AND status<2");
    if ($CLD_CON2->GetRsRows() > 0) {
        $num_incid = $CLD_CON2->GetRsRows();
        echo "<span class='incidPop'> $num_incid</span>";
    }
    echo "</div>";
    echo "</div>";
}



?>


