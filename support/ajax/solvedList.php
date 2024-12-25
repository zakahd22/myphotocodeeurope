<?php
include '../sessio.php';
include '../conexio.php';
$CLD_CON2 = clone($CLD_CON);
$where = "WHERE p.solved=1";
if($_SESSION['USERID'] == '9999991' ){
    if(isset($_POST['owner'])){
    $owner= $_POST['owner'];
    if($owner!=0){
    $where = "WHERE p.solved=1 AND p.propietari = $owner";
    }
    }
}else{
    $owner= $_SESSION['USERID'];
    $where = "WHERE p.solved=1 AND p.propietari = $owner";
}

if(isset($_POST['filtre'])){
$booth = $_POST['booth'];
$boothType = $_POST['boothType'];
$dateS = $_POST["dateS"];
$dateE = $_POST["dateE"];
if($booth != 0){
    $where .= " AND p.booth_id=$booth";
}
if(!empty($dateE) && !empty($dateS)){
    $where .= " AND p.dataTiempo BETWEEN '$dateS 00:00:00' AND '$dateE 23:59:59'";
}
if($boothType!= '0'){
    $where .= " AND p.boothType = '$boothType' ";
}

}
 
?>

<ul>
                            <?php
                            
                            $CLD_CON->OpenRs("SELECT p.id as IDN , p.booth_id , p.dataTiempo , bt.name as nombre FROM SAT_problems p LEFT JOIN booth_types bt ON bt.char = p.boothType   $where ORDER BY p.dataTiempo DESC");
                            while($CLD_CON->FetchArray()){
								$serialnumber = "";
                                $id = $CLD_CON->GetArrayField("IDN");
                                $data = $CLD_CON->GetArrayField("dataTiempo");
                                $typeName = $CLD_CON->GetArrayField("nombre");
								$photoBooth = $CLD_CON->GetArrayField("booth_id");
								$CLD_CON2->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth = $photoBooth");
								if($CLD_CON2->FetchArray()){
									$serialnumber = "- " . $CLD_CON2->GetArrayField("serialnumber");
								}								
                                echo "<li onclick='showProblemProfile($id)'>";
                                echo "$data - $typeName $serialnumber";
                                echo "</li>";
                            }
                            
                            ?>

</ul>

