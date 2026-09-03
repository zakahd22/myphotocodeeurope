<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

$ID = $_POST['id'];
$p = $_POST['preference'];
$o = $_POST['owner'];
$type= $_POST['type'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT id , preference FROM App_ownerAddress WHERE preference<$p AND idOwner= $o AND CLD_type=$type");
while($CLD_CON->FetchArray()){
    $idS = $CLD_CON->GetArrayField("id");
    $preference = $CLD_CON->GetArrayField("preference") + 1;
    $CLD_CON2->Execute("UPDATE App_ownerAddress SET preference=$preference WHERE id=$idS");
}

if($CLD_CON->Execute("UPDATE App_ownerAddress SET preference=1 WHERE id=$ID")){
    echo "OK";
}else{
    echo "ERROR.";
}

?>