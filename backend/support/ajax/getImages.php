<?php
include "../sessio.php";
include '../conexio.php';
$qs = $_POST['qs'];
$id= $_POST['id'];
if($qs==1){
    $where = "question=$id";    
}else{
    $where = "solution=$id";
}

$CLD_CON->OpenRs("SELECT ruta FROM SAT_media WHERE $where AND tipo=1");
echo "<ul id='myGallery'>";
while($CLD_CON->FetchArray()){
    $r= $CLD_CON->GetArrayField("ruta");
    echo "<li><img src='$r'/></li>";
}
echo "</ul>";


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
