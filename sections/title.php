<?php
include '../sessio.php';
require_once G_PATH . 'common/conexio.php';
$s= $_POST['s'];
$ID= $_POST['id'];
if($s=="owner"){
    $CLD_CON->OpenRs("SELECT name FROM rentals WHERE id=$ID");
    if($CLD_CON->FetchArray()){
        echo $CLD_CON->GetArrayField("name");
    }
}
if($s=="events"){
    $CLD_CON->OpenRs("SELECT title FROM events WHERE id=$ID");
    if($CLD_CON->FetchArray()){
        echo $CLD_CON->GetArrayField("title");
    }
}
if($s=="photobooths"){
    
    $CLD_CON->OpenRs("SELECT serialnumber ,CLD_idType FROM App_booths WHERE idBooth=$ID");
    if($CLD_CON->FetchArray()){
        $sn =  $CLD_CON->GetArrayField("serialnumber");
        $idType =  $CLD_CON->GetArrayField("CLD_idType");
    }
    if(!empty($idType)){
        $CLD_CON->OpenRs("SELECT name FROM CLD_boothTypes WHERE id = $idType");
         if($CLD_CON->FetchArray()){
               $TypeName =  $CLD_CON->GetArrayField("name");
               echo $TypeName . "($sn)";
         }
    }else{
        echo $sn;
    }
    
}
if($s=="components"){
    $CLD_CON->OpenRs("SELECT type FROM CLD_components WHERE serialnumber='$ID'");
    if($CLD_CON->FetchArray()){
        $type= $CLD_CON->GetArrayField("type");
    }
     $CLD_CON->OpenRs("SELECT descripcio FROM CLD_typeComponents WHERE id=$type");
    if($CLD_CON->FetchArray()){
        $typeName = $CLD_CON->GetArrayField("descripcio");
    }
echo $typeName . " " . $ID;
}
?>
