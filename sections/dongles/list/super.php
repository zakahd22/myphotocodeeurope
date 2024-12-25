<?php

$CLD_CON2 = clone($CLD_CON);

$baseController = new baseController();
$baseController->createModel('booths');
$baseController->createModel('rentals');
$baseController->createModel('App_boothDongle');


if (isset($_POST['fil'])) {
    $str = $_POST['code'];
    if (strlen($str) == 4) {
        $str = substr($str, -3);
    }
    $select_nolimit = "SELECT b.* , r.name , r.id as rid FROM booths b LEFT JOIN rentals r ON b.rental_id=r.id WHERE b.rand_string='$str'";
    $booths = $baseController->boothsModel->getBoothOwners($str, $LIMIT);
    $countBooths = count($booths["booths"]);
} 
else {
    $select_nolimit = "SELECT b.* , r.name , r.id as rid FROM booths b LEFT JOIN rentals r ON b.rental_id=r.id ";
    $booths = $baseController->boothsModel->getBoothOwners(false, $LIMIT);
    $countBooths = count($booths["booths"]);
}

$x = 0;
while($x < $countBooths){
//    foreach ($booths as $booth){
    $dongleID = $booths["booths"][$x]["id"];
    $dongle = $booths["booths"][$x]["dongle"];
    $reference = $booths["booths"][$x]["reference"];
    $dongleString = $booths["booths"][$x]["rand_string"];
    $rental_id = $booths["booths"][$x]["rental_id"];
    $dongleOwner = $booths["rentals"][$x]["name"];
//        $rental = $baseController->rentalsModel->getRentalsNames($rental_id);
//        $rental_name = stripslashes($rental[0]["name"]);

    $boothDongleData = $baseController->App_boothDongleModel->getBoothDongle($dongleID);
    if ($boothDongle) {
        $boothdongleID = $boothDongleData[0][0]["idBooth"];
        $boothDongle = stripslashes($boothDongleData[1][0]["name"]) . " " . $boothDongleData[1][0]["serialnumber"];
    } 
    else {
        $boothdongleID = "";
        $boothDongle = "";
    }

    echo "<ul class='regDongleUL'>";
    echo "<li  style='width:19%;' title='Dongle numbers'>$dongle</li>";
    echo "<li  style='width:19%;' title='Reference'$reference</li>";
    echo "<li  style='width:19%;' title='Dongle String'>$dongleString</li>";

    if (empty($dongleOwner)) {
        echo "<li  style='width:19%' title='Owner'>$dongleOwner - </li>";
    } 
    else {
        echo "<li style='width:19%' title='Owner'>";
        if($USERTYPE != 1){
            echo "<span class='link'>$dongleOwner</span></li>";
        } 
        else {
            echo "<span class='link' onclick='openLink(\"Owner\" , \"$rental_id\")'>$dongleOwner</span></li>";
        }
    }
    if (empty($boothDongle)) {
        echo "<li  style='width:19%' title='Last PhotoBooth'> - </li>";
    } 
    else {
        echo "<li  style='width:19%' title='Last PhotoBooth'>";
        echo "<span class='link' onclick='openLink(\"PhotoBooths\" , \"$boothdongleID\");'>$boothDongle</span></li>";
    }
    echo "</ul>";
    $x++;
}

echo "</div>";

$s = "dongles";
$color = "orange";
include '../../pagescount.php';

/*
while ($CLD_CON->FetchArray()) {
    $dongleID = $CLD_CON->GetArrayField("id");
    $dongle = $CLD_CON->GetArrayField("dongle");
    $reference = $CLD_CON->GetArrayField("reference");
    $dongleString = $CLD_CON->GetArrayField("rand_string");
    $dongleOwner = stripslashes($CLD_CON->GetArrayField("name"));
    $dongleOwnerId = $CLD_CON->GetArrayField("rid");
    $CLD_CON2->OpenRs("SELECT bd.idBooth , b.name , b.serialnumber FROM App_boothDongle bd LEFT JOIN App_booths b ON bd.idBooth = b.idBooth WHERE idDongle = $dongleID ORDER BY bd.datetimeS DESC LIMIT 1");
    if ($CLD_CON2->FetchArray()) {
        $boothdongleID = $CLD_CON2->GetArrayField("idBooth");
        $boothDongle = stripslashes($CLD_CON2->GetArrayField("name")) . " " . $CLD_CON2->GetArrayField("serialnumber");
    } else {
        $boothdongleID = "";
        $boothDongle = "";
    }

    echo "<ul class='regDongleUL'>";
    echo "<li  style='width:19%;' title='Dongle numbers'>$dongle</li>";
    echo "<li  style='width:19%;' title='Reference'$reference</li>";
    echo "<li  style='width:19%;' title='Dongle String'>$dongleString</li>";

    if (empty($dongleOwner)) {
        echo "<li  style='width:19%' title='Owner'>$dongleOwner - </li>";
    } else {
        echo "<li style='width:19%' title='Owner'>";
        if($USERTYPE != 1){
            echo "<span class='link'>$dongleOwner</span></li>";
        } else {
            echo "<span class='link' onclick='openLink(\"Owner\" , $dongleOwnerId)'>$dongleOwner</span></li>";
        }
    }
    if (empty($boothDongle)) {
        echo "<li  style='width:19%' title='Last PhotoBooth'> - </li>";
    } else {
        echo "<li  style='width:19%' title='Last PhotoBooth'>";
        echo "<span class='link' onclick='openLink(\"PhotoBooths\" , $boothdongleID);'>$boothDongle</span></li>";
    }

    echo "</ul>";
}

echo "</div>";

$s = "dongles";
$color = "orange";
include '../../pagescount.php';
?>
*/