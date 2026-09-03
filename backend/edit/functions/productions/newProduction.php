<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$typeBooth = $_POST['type'];
$n = $_POST['n'];
$date = date("Y-m-d");
$CLD_CON->OpenRs("SELECT * FROM CLD_boothTypes WHERE id=$typeBooth ");
if($CLD_CON->FetchArray()){
    $nameBooth = $CLD_CON->GetArrayField("name");
    $boothChar = $CLD_CON->GetArrayField("char");
    $boothSNnum = $CLD_CON->GetArrayField("CLD_modelSN");
    $lastSN = $CLD_CON->GetArrayField("CLD_lastSN");
}

if($productionID = $CLD_CON->ExecuteInsert("INSERT INTO CLD_Productions(boothType , num_products , startDate , status) VALUES('$nameBooth' , $n , '$date' , 0)")){
    $x=1;
    while($x<=$n){
        $snfi = $lastSN + $x;
        $t = true;
        if($snfi/10 < 0){
            $snfi2 = "000".$x;
            $t=false;
        }
        if($snfi/100 < 0){
            $snfi2 = "00".$x;
             $t=false;
        }
        if($snfi/1000 < 0){
            $snfi2 = "0".$x;
             $t=false;
        }
        if($t){
            $snfi2 = $snfi;
        }
        $serialnumber = "00" . $boothSNnum . "000" . $boothSNnum . $snfi . "-P";
        $name = $boothChar . "-" .  $boothSNnum . $snfi;        
        $CLD_CON->Execute("INSERT INTO App_booths (type , owner , name , serialnumber , CLD_Status , CLD_date_start_production , CLD_idType ,CLD_production) VALUES('$boothChar' , 1 , '$name' , '$serialnumber' , 0 , '$date' , $typeBooth , $productionID)");     
        $CLD_CON->Execute("UPDATE CLD_boothTypes SET CLD_lastSN=$snfi WHERE id=$typeBooth");
        $x++;
    }    
    
    echo "OK";
}else{
    echo "Error , hi hagut un error creant la produccio torna-ho a prova";
}

?>
