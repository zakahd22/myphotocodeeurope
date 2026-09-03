<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$distributors = array('','DC', 'DCA', 'MATT');
if(isset($_POST['fil'])){
    $NM = $_POST['nameOwner'];
    $WHERE1 = "WHERE name LIKE '%$NM%'"; 
    $WHERE2 = "AND name LIKE '%$NM%'"; 
}else{
    $WHERE1 = ""; 
    $WHERE2 = ""; 
}


if ($_SESSION['USERTYPE'] == 1) {
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals $WHERE1 ORDER BY name ";
}
if ($_SESSION['USERTYPE'] == 2) {
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals $WHERE1 ORDER BY name";
}
if ($_SESSION['USERTYPE'] == 6) {
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals $WHERE1 ORDER BY name";
}
if ($_SESSION['USERTYPE'] == 3) {
    $disID = $_SESSION['USERID'];
    $consulta = "SELECT id , name , CLD_DistributorId , App_email FROM rentals WHERE CLD_DistributorId = $disID $WHERE2 ORDER BY name";
}
$CLD_CON->OpenRs($consulta);
while ($CLD_CON->FetchArray()) {
    $ownerID = $CLD_CON->GetArrayField("id");
    $companyName = $CLD_CON->GetArrayField("name");
    $distributor = $CLD_CON->GetArrayField("CLD_DistributorId");
    $ownerEmail = $CLD_CON->GetArrayField("App_email");

        echo "<div class='popup-row list-items'>";
            echo "<div id='listGroup' class='popup-row'>";
                echo "<div id='selectorOwner' class='popup-col'>";
                    echo "<input type='radio' value='$ownerID' name='ownerID' style='margin-right:10px;margin-left:10px;display:inline;float:left;height:80px;width:30px;'>";
                    echo "<input type='hidden' value='$companyName' id='o$ownerID'>";
                echo "</div>";
                echo "<div id='contentGroup' class='popup-row'>";
                    echo "<div id='imageOwner' class='popup-col'>";
                        if (file_exists("../images/ownerIMG/$ownerID.jpg")) {
                            echo "<img src='images/ownerIMG/$ownerID.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
                        } else {
                            echo "<img src='images/ownerIMG/noPimg.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
                        }
                    echo "</div>";
                    echo "<div id='OwnerInfo' class='popup-col'>";
                        if (empty($ownerEmail)) {
                            echo $companyName . " (No Contact Email - email No recivied)";
                        } else {
                            echo $companyName . " (ContactEmail : $ownerEmail)";
                        }
                    echo "</div>";
                echo "</div>";
            echo "</div>";
            echo "<div id='distributorOwner' class='popup-col'>";
                if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6 ) {
                    $dName = $distributors[$distributor];
                    echo "<span style='float:right;margin-right:10px;'>$dName</span>";
                }
            echo "</div>";
        echo "</div>";
    
//    echo "<p style='line-height:80px;font-size:12pt;'>";
//    echo "<input type='radio' value='$ownerID' name='ownerID' style='margin-right:10px;margin-left:10px;display:inline;float:left;height:80px;width:30px;'>";
//    echo "<input type='hidden' value='$companyName' id='o$ownerID'>";
//    if (file_exists("../images/ownerIMG/$ownerID.jpg")) {
//        echo "<img src='images/ownerIMG/$ownerID.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
//    } else {
//        echo "<img src='images/ownerIMG/noPimg.jpg' style='height:80px; display:inline;float:left;margin-right:20px;'>";
//    }
//    if (empty($ownerEmail)) {
//        echo $companyName . " (No Contact Email - email No recivied)";
//    } else {
//        echo $companyName . " (ContactEmail : $ownerEmail)";
//    }
//    if ($_SESSION['USERTYPE'] < 3 || $_SESSION['USERTYPE']==6 ) {
//        $dName = $distributors[$distributor];
//        echo "<span style='float:right;margin-right:10px;'>$dName</span>";
//    }
//    echo "</p>";
}
?>
