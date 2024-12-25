<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$e = "";
$x=true;$y=true;
$ID = $_POST['id'];
$cQuality = $_POST['cQuality'];
$date = date("Y-m-d H:i:s");

$CLD_CON->OpenRs("SELECT * FROM App_booths WHERE idBooth='$ID' AND CLD_Status=0");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $idType = $CLD_CON->GetArrayField("CLD_idType");
    if (empty($idType)) {
        $model = substr($sn, 2, 2);
        $CLD_CON->OpenRs("SELECT id , name FROM CLD_boothTypes WHERE CLD_modelSN = $model");
        if ($CLD_CON->FetchArray()) {
            $idType = $CLD_CON->GetArrayField("id");
        }
    }
}

$CLD_CON->OpenRs("SELECT id_component , quantitat FROM CLD_boothsComponents WHERE id_typeBooth=$idType AND optional=0;");
while ($CLD_CON->FetchArray()) {
    $cmp = $CLD_CON->GetArrayField("id_component");
    $qty = $CLD_CON->GetArrayField("quantitat");
    $CLD_CON2->OpenRs("SELECT * FROM CLD_components WHERE type=$cmp AND booth=$ID");
    $q = $CLD_CON2->GetRsRows();
    $CLD_CON3->OpenRs("SELECT  descripcio FROM CLD_typeComponents WHERE id=$cmp");
    if ($CLD_CON3->FetchArray()) {
        $cmpDes = $CLD_CON3->GetArrayField("descripcio");
    }
    if ($q < $qty) {
        $e .="Falten " . ($qty - $q) . " $cmpDes";
        $x = false;
    }
}

if($x){    
    if(isset($_POST['noDongle'])){
        $date = date("Y-m-d H:i:s");
        $CLD_CON->Execute("UPDATE App_booths SET   CLD_idType=$idType  , CLD_Status=1 , CLD_ControlQuality='$cQuality' , CLD_date_production='$date' WHERE idBooth=$ID");
         $coment = "Change from production to Finish Product";
        $CLD_CON->Execute("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn ) VALUES('$coment' , '$date' , $ID , $sn)");
        echo "OK";
        
    }else{
    $dongle = $_POST['dongleString'];
    $CLD_CON->OpenRs("SELECT id FROM booths WHERE rand_string='$dongle' AND rental_id=1");
    if($CLD_CON->FetchArray()){
        $dongleID = $CLD_CON->GetArrayField("id");        
        $CLD_CON2->OpenRs("SELECT idBooth  FROM App_boothDongle WHERE idDongle=$dongleID AND datetimeF IS NULL ORDER BY datetimeS DESC");
        if($CLD_CON2->FetchArray()){
            $ghostBooth = $CLD_CON2->GetArrayField("idBooth");
            //$sn_ghost = $CLD_CON2->GetArrayField("serialnumber");
            $CLD_CON3->OpenRs("SELECT * FROM App_booths WHERE CLD_Status=-1 AND idBooth=$ghostBooth");
            if($CLD_CON3->FetchArray()){
                $date = date("Y-m-d H:i:s");
                $CLD_CON->Execute("UPDATE CLD_historyBooth SET idBooth=$ghostBooth WHERE idBooth=$ID");
                $CLD_CON->Execute("UPDATE CLD_components SET booth=$ghostBooth WHERE booth=$ID");
                $CLD_CON->Execute("UPDATE CLD_Incidents SET idBooth=$ghostBooth WHERE idBooth=$ID");
                $CLD_CON->Execute("UPDATE SAT_problems SET booth_id =$ghostBooth WHERE booth_id=$ID");
                $CLD_CON->Execute("DELETE FROM App_booths WHERE idBooth=$ghostBooth");
                $CLD_CON->Execute("UPDATE App_booths SET idBooth=$ghostBooth , CLD_idType=$idType  , CLD_Status=1 , CLD_ControlQuality='$cQuality' , CLD_date_production='$date' WHERE idBooth=$ID");
                $coment = "Change from production to Finish Product";
                $CLD_CON->Execute("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn ) VALUES('$coment' , '$date' , $ghostBooth , $sn)");
                echo "OK";
            }else{
                if($ghostBooth != $ID){
                 $y=false;
                 $e .= "El Ghost PhotoBooth no esta en estat Pending la id es $ghostBooth , $sn_ghost";
                }else{
                $CLD_CON->Execute("UPDATE App_booths SET idBooth=$ghostBooth , CLD_idType=$idType  , CLD_Status=1 , CLD_ControlQuality='$cQuality' , CLD_date_production='$date' WHERE idBooth=$ID");
                $coment = "Change from production to Finish Product";
                $CLD_CON->Execute("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn ) VALUES('$coment' , '$date' , $ghostBooth , $sn)");
                echo "OK";
                }
            }
            
            
        }else{
            $y=false;
            $e .= "No he trobat el Ghost PhotoBooth";
        }     
    }else{
        $y=false;
        $e .= "El dongle $dongle no existeix a la base de dades";
    }
    }
    
    if(!$y){
        echo $e;
    }
}else{
        echo $e;
        
}
?>
