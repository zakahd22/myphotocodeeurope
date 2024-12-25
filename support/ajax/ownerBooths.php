<?php
include '../sessio.php';
require_once G_PATH . 'common/conexio.php';

$rental = $_POST['owner'];
echo "rental : " . $rental;
$CLD_CON->OpenRs("SELECT b.idBooth , b.CLD_idType , b.serialnumber, b.name , bt.name as tipoNom FROM App_booths b LEFT JOIN CLD_boothTypes bt ON bt.id=b.CLD_idType WHERE owner=$rental ORDER BY bt.name");
while($CLD_CON->FetchArray()){
    $id = $CLD_CON->GetArrayField("idBooth");
    $ch = $CLD_CON->GetArrayField("type");
    $name = $CLD_CON->GetArrayField("name");
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $typeName = $CLD_CON->GetArrayField("tipoNom");    
    echo "<option value='$id-$ch'>$sn - $typeName - $name</option>";
}

                            
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
