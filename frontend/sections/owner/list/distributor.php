<?php

$baseController = new baseController();
$baseController->createModel('App_booths');
$baseController->createModel('booths');
$baseController->createModel('rentals');
$baseController->createModel('CLD_Distributors');


$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

$distributor = $USERID;


if(isset($_POST['filPage'])){
    $where = $_SESSION['WH'];
    //$CLD_CON->OpenRs("SELECT * FROM rentals $where ORDER BY name LIMIT $LIMIT");
    $select_nolimit = "SELECT * FROM rentals $where ORDER BY name";
    $owners = $baseController->rentalsModel->getRentalsListDistr($USERID, $cName, $LIMIT);
    $totalrows = count($baseController->rentalsModel->getRentalsListDistr($USERID, $cName));
}
else{
    $whereDistributor = "(CLD_DistributorId = $USERID OR rentals.id IN (SELECT owner FROM App_booths WHERE CLD_Distributor = {$USERID})) ";
    if(isset($_POST['cName'])){
        $cName = $_POST['cName'];
        $select_nolimit = "SELECT * FROM rentals WHERE {$whereDistributor} AND name LIKE '%$cName%' ORDER BY name";
        $_SESSION['WH'] = "WHERE {$whereDistributor} AND name LIKE '%$cName%' ";
        $owners = $baseController->rentalsModel->getRentalsListDistr($USERID, $cName, $LIMIT);
        $totalrows = count($baseController->rentalsModel->getRentalsListDistr($USERID, $cName));
    }
    else{        
        $select_nolimit = "SELECT * FROM rentals WHERE {$whereDistributor} ORDER BY name";
        $owners = $baseController->rentalsModel->getRentalsListDistr($USERID, false, $LIMIT);
        //$count_no_limits = $baseController->rentalsModel->getRentalsInfo($owners);
        $totalrows = count($baseController->rentalsModel->getRentalsListDistr($USERID));
    }
}

$html = "<link rel='stylesheet' href='sections/owner/resources/owner.css' type='text/css'>";
$html .= "<div id='positional_div'></div>";
$i=0;
foreach ($owners as $owner){
    $idOwner = $owner["id"];
    $ownerName = $owner["name"];
    $userName = $owner["username"];
    $i++;
    $Distributorid = $owner["CLD_DistributorId"];
    
    $profileIMG_ruta = "../../../images/ownerIMG/$idOwner.jpg";
    
    if (file_exists($profileIMG_ruta)) $profileIMG = "images/ownerIMG/$idOwner.jpg"; 
    else $profileIMG = "images/ownerIMG/noPimg.jpg";
    
    $distributor = $baseController->CLD_DistributorsModel->getDistributorName($Distributorid);
    if($distributor) $distributor = $distributor[0]["Name"];
    else $distributor = "Undefined";
    
    $html .= "<div class='regOwner' onclick='setSection(\"owner\" ,2 ,$idOwner)'>";
    $html .= "<div class='imgListOwner'>";
    $html .= "<img src='$profileIMG' class='owner_img'>";
    $html .= "</div>";
    $html .= "<div class='infoListOwner'>";
    $html .= "<p>Owner Name : $ownerName</p>";
    $html .= "<p>Username : $userName</p>";
    $html .= "<p>Distributor : $distributor</p>";
    $html .= "</div>";
    $html .= "</div>";   
}

echo $html;

$s = "owner";
$color="#378DE8";
include '../../pagescount.php';