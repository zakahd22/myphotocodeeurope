<?php 
include '../sessio.php';
require_once G_PATH . 'common/conexio.php';


$USERNAME =  $_SESSION['USERNAME'];
$userType = $_SESSION['USERTYPE'];
$userID =  $_SESSION['USERID'];

if(isset($_POST['typeBooth'])){
    $boothType = $_POST['typeBooth'];
}

$CLD_CON->OpenRs("SELECT b.idBooth , b.serialnumber ,  b.type , b.name , bt.name as tipoNom FROM App_booths b LEFT JOIN CLD_boothTypes bt ON bt.id=b.CLD_idType WHERE b.owner={$userID} AND b.CLD_idType={$boothType} ORDER BY bt.name");
if($CLD_CON->GetRsRows()==0){
  //  echo "<option value='{$boothType}'>userId= {$userID} boothType={$boothType}</option>";
    echo "<option value='0-{$boothType}'>UNDEFINED BOOTH</option>";
}

while($CLD_CON->FetchArray()){
    $id = $CLD_CON->GetArrayField("idBooth");
    $ch = $CLD_CON->GetArrayField("type");
    $name = $CLD_CON->GetArrayField("name");
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $typeName = $CLD_CON->GetArrayField("tipoNom");
    echo "<option value='{$id}-{$ch}'>{$sn} - {$typeName} - {$name}</option>";
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */