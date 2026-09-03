<?php

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

$baseController = new baseController();
$baseController->createModel('rentals');
$baseController->createModel('CLD_Distributors');


if(isset($_POST['filPage'])){
    $where = $_SESSION['WH'];
    $CLD_CON->OpenRs("SELECT * FROM rentals $where ORDER BY name LIMIT $LIMIT");
    $select_nolimit = "SELECT * FROM rentals $where ORDER BY name";
}
else{
    if(isset($_POST['cName'],$_POST['uName'])){
        $cName = $_POST['cName'];
        $uName = $_POST['uName'];
        
        $select_nolimit = "SELECT * FROM rentals WHERE name LIKE '%$cName%' ORDER BY name";
        $_SESSION['WH'] = "WHERE name LIKE '%$cName%'";   
        $where = $_SESSION['WH'];
        
        $rentals = $baseController->rentalsModel->getRental($cName, $uName, $LIMIT);
//        $events = $baseController->eventsModel->getAllFromEventsListIn($in, $tt, $id, $owner);
        $totalrows=count($rentals);
    }
    else{
        $select_nolimit = "SELECT * FROM rentals ORDER BY name"; 
        $count_no_limits = $baseController->rentalsModel->getAllRentals("count");
        $totalrows = $count_no_limits[0]["counter"];
        
        $rentals = $baseController->rentalsModel->getAllRentals("all", $LIMIT);
    }
}

$html = "<link rel='stylesheet' href='sections/owner/resources/owner.css' type='text/css'>";
$html .= "<div id='positional_div'></div>";

foreach ($rentals as $rental){
    
    $idOwner       = $rental["id"];
    $ownerName     = $rental["name"];
    $userName      = $rental["username"];
    $Distributorid = $rental["CLD_DistributorId"];
    
    $profileIMG_ruta = "../../../images/ownerIMG/$idOwner.jpg";
    
    if (file_exists($profileIMG_ruta)) $profileIMG = "images/ownerIMG/$idOwner.jpg";
    else $profileIMG = "images/ownerIMG/noPimg.jpg";
    
    $distributor = $baseController->CLD_DistributorsModel->getDistributorName($Distributorid);
    if($distributor){
        $distributor = $distributor[0]["Name"];
    }
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
