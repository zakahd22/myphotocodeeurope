<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$SN = $_POST['id'];


$CLD_CON->OpenRs("SELECT * FROM CLD_components WHERE serialnumber='$SN'");
if ($CLD_CON->FetchArray()) {
    $type = $CLD_CON->GetArrayField("type");
    $boothID = $CLD_CON->GetArrayField("booth");
    $distributorID = $CLD_CON->GetArrayField("distributor");
    $ownerID = $CLD_CON->GetArrayField("owner");
    $dataEntrada = date("F d, Y", strtotime($CLD_CON->GetArrayField("data_entrada")));
    $dataDis2 = $CLD_CON->GetArrayField("data_distribuidor");
    if (empty($dataDis2)) {
        $dataDis = '<p> Date to Distributor : Unknow</p>';
    } else {
        $dataDis = "<p> Date to Distributor :". date("F d, Y", strtotime($CLD_CON->GetArrayField("data_distribuidor"))) ."</p>";
    }
    $dataOwner2= $CLD_CON->GetArrayField("data_owner");
    if (empty($dataOwner2)) {
        $dataOwner = '<p>Date to Owner : Unknow</p>';
    } else {
        $dataOwner = "<p>Date to Owner :" . date("F d, Y", strtotime($CLD_CON->GetArrayField("data_owner"))) . "</p>";
    }
}
if($_SESSION['USERTYPE'] < 3){
   $editDistributor = "<input type='button' class='editButton' onClick='edit(60 , \"$SN\");'> ";
   $editOwner = "<input type='button' class='editButton' onClick='edit(59 , \"$SN\");'> ";
}

$CLD_CON->OpenRs("SELECT descripcio FROM CLD_typeComponents WHERE id=$type");
if ($CLD_CON->FetchArray()) {
    $typeName = $CLD_CON->GetArrayField("descripcio");
}
if (empty($ownerID)) {
    $owner = "<p>Owner : No-Owner ".$editOwner."</p>";
} else {
    $CLD_CON->OpenRs("SELECT name FROM rentals WHERE id=$ownerID");
    if ($CLD_CON->FetchArray()) {

        $owner = "<p>Owner : <span class='link2' onclick='openLink(\"Owner\" , $ownerID)'>" . $CLD_CON->GetArrayField("name") . "</span> ".$editOwner."</p>".$dataOwner;
    } else {
        $owner = "<p>Owner: No-owner ".$editOwner."<p>";
    }
}


if (empty($distributorID)) {
    $distributor = "<p>Distributor : No Distributor ". $editDistributor . "</p>";
} else {
    $CLD_CON->OpenRs("SELECT Name FROM CLD_Distributors WHERE id=$distributorID");
    if ($CLD_CON->FetchArray()) {


        $distributor = "<p>Distributor : " . $CLD_CON->GetArrayField("Name") . $editDistributor . "</p>" . $dataDis;
    } else {
        $distributor = "<p>Distributor : No Distributor ". $editDistributor . "</p>";
    }
}
echo "<div class='inContent'>";
    echo "<div class='boxLeft'>";
        echo "<h1>$typeName INFO</h1>";
        echo "<div class='box'>";
            echo "<div class='imgProfileBooth'>";
                echo "<img src='images/web/components/c$type.png' style='width:100%;'>";
            echo "</div>";
            echo "<div class='infoProfileBooth'>";
                echo "<p> $typeName - $SN</p>";
                echo "<p> Entry : $dataEntrada </p>";
                if($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6 ){
                echo $distributor;
                }
                echo $owner;
            echo "</div>";
        echo "</div>";
    echo "</div>";
    echo "<div class='boxRight'>";        
        echo "<h1> History  :</h1>";
        echo "<div class='box' style='border:1px solid gray; max-height:80%;overflow:auto;'>";
            $CLD_CON->OpenRs("SELECT * FROM CLD_historyComponents WHERE component_sn='$SN' ORDER BY data DESC");
            while($CLD_CON->FetchArray()){
                $coment = stripslashes($CLD_CON->GetArrayField("comment"));
                $dat = $CLD_CON->GetArrayField("data");
                $data = date("F d, Y | H:i:s", strtotime($dat));
                echo "<p>$data - $coment</p>";
                echo "<hr>";
            }
        echo "</div>";
    echo "</div>";
echo "</div>"
?>
