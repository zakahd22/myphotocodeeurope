<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";


$baseController = new baseController();
$baseController->createModel('App_alerts');
$baseController->createModel('App_boothAlert');
$baseController->createModel('rentals');
$baseController->createModel('App_boothConfigDef');
$baseController->createModel('App_booths');
$baseController->createModel('App_boothAlertDef');

$alerts = $baseController->App_alertsModel->getAlerts();
foreach ($alerts as $alert){
    $alert_type =  $alert["typeAlert"];
    $alert_text =  $alert["textAlert"];
    $types_[$alert_type]= $alert_text;
}

$ID = $_POST["id"];
$CLD_CON2 = clone($CLD_CON);

echo "<div class='inContent'>";
//$CLD_CON->OpenRS("SELECT * FROM App_boothAlert x WHERE x.estat<2 AND x.idBooth=$ID ORDER BY x.when DESC");
echo "<h1>UNSOLVED ALERTS:</h1>";
echo "<div class='noSolvedAlert'>";

$boothalerts = $baseController->App_boothAlertModel->getboothAlerts($ID);
if($boothalerts == FALSE){
    echo "This Photobooth is OK";
}
else {
    foreach ($boothalerts as $boothalert){
        $when =  $boothalert["when"];
        $i =  $boothalert["typeAlert"];
        $estat = $boothalert["estat"];
        
        if($estat < 2){
            if ($i == 11) {
                echo "<p class='alertsOrange'>$when - The PhotoBooth is running out the film</p>";
            }
            if ($i == 12) {
                echo "<p class='alertsOrange'>$when- The CashBox is getting full  </p>";
            }
            if ($i == 1) {
                 echo "<p class='alertsOrange'>$when - The PhotoBooth is offline</p>";
            }
        }        
    }  
}
echo "</div>";
echo "<h1>Soveld Alerts:</h1>";
echo "<div class='solvedAlert'>";

if($boothalerts == FALSE){
    echo "This Photobooth is OK";
}
else {
    foreach ($boothalerts as $boothalert){
        $when =  $boothalert["when"];
        $i =  $boothalert["typeAlert"];
        $estat = $boothalert["estat"];
        
        if($estat == 2){
            if ($i == 11) {
                echo "<p class='alertsOrange'>$when - The PhotoBooth was running out the film.</p>";
            }
            if ($i == 12) {
                echo "<p class='alertsOrange'>$when- The CashBox was getting full.  </p>";
            }
            if ($i == 1) {
                 echo "<p class='alertsOrange'>$when - The PhotoBooth  was offline.</p>";
            }
        }
    }
    
}

//$CLD_CON->OpenRS("SELECT * FROM App_boothAlert x WHERE x.estat=2 AND x.idBooth=$ID ORDER BY x.when DESC");
//while ($CLD_CON->FetchArray()) {
//    $i = $CLD_CON->GetArrayField("typeAlert");
//    $when = $CLD_CON->GetArrayField("when");
//    if ($i == 11) {
//        echo "<p class='alertsOrange'>$when - The PhotoBooth was running out the film.</p>";
//    }
//    if ($i == 12) {
//        echo "<p class='alertsOrange'>$when- The CashBox was getting full.  </p>";
//    }
//    if ($i == 1) {
//         echo "<p class='alertsOrange'>$when - The PhotoBooth  was offline.</p>";
//    }
//}
echo "</div>";
echo "<div class='alertsPhotobooth'>";




$owner = $baseController->rentalsModel->getOwnersBooth($ID);
//$CLD_CON2->OpenRs("SELECT r.App_email FROM rentals r LEFT JOIN App_booths b ON b.owner=r.id WHERE b.idBooth=$ID");
if ($owner) {
    $emailAlerts = $owner[0]["App_email"];

    echo "<h2>ALERT EMAIL :</h2>";
    echo "<p> Your Alert email is $emailAlerts. (Set it in your profile)</p>";

    
    $alert11 = $baseController->App_boothAlertDefModel->getAlerts($ID, 11);
    //$alert11 = $CLD_CON->OpenRs("SELECT value FROM App_boothAlertDef WHERE idBooth=$ID AND typeAlert=11");
    if ($alert11) {
        $val = $alert11[0]["value"];
        $editaAlertFilm = "<input type='button' class='editButton' onClick='edit(10 , $ID);'>";
        if ($_SESSION['USERTYPE']==6) {
            $editaAlertFilm = "";
        }
        if ($val == "none") {
            echo "<h2>FILM ALERT IS <span style='color:red;'>OFF</span> ";
            echo $editaAlertFilm;
            echo "</h2>";
            echo "<p>The Film Alert is OFF.</p>";
        } else {
            echo "<h2>FILM ALERT IS <span style='color:green;'>ON</span> ";
            echo $editaAlertFilm;
            echo "</h2>";
            echo "<p>When the film stock falls below $val units, you will receive an alert.</p>";
        }
    } else {
            echo "<h2>FILM ALERT IS <span style='color:red;'>OFF</span> ";
            echo "<input type='button' class='editButton' onClick='edit(10 , $ID);'>";
            echo "</h2>";
            echo "<p>The Film Alert is OFF.</p>";
    }
    $alert12 = $baseController->App_boothAlertDefModel->getAlerts($ID, 12);
    //$CLD_CON->OpenRs("SELECT value FROM App_boothAlertDef WHERE idBooth=$ID AND typeAlert=12");
    if ($alert12) {
        $val = $alert12[0]["value"];
        $editaAlertMoney = "<input type='button' class='editButton' onClick='edit(11 , $ID);'>";
        if ($_SESSION['USERTYPE']==6) {
            $editaAlertMoney = "";
        }
        if ($val == "none") {
            echo "<h2>MONEY ALERT IS <span style='color:red;'>OFF</span> ";
            echo $editaAlertMoney;
            echo "</h2>";
            echo "<p>The money alert is OFF. </p>";
        } else {
            echo "<h2>MONEY ALERT IS <span style='color:green;'>ON</span> ";
            echo $editaAlertMoney;
            echo "</h2>";
            echo "<p>When the cash box reaches $val $/€/&pound;/kr... , you will receive an alert. </p>";
        }
    } else {
        echo "<h2>MONEY ALERT IS <span style='color:red;'>OFF</span> ";
        echo "<input type='button' class='editButton' onClick='edit(11 , $ID);'>";
            echo "</h2>";
        echo "<p>The money alert is OFF.</p>";
    }

    $alertOffline = $baseController->App_boothsModel->getBoothID($ID);
    //$CLD_CON->OpenRs("SELECT timeZone, alertOffline, hS , mS ,hE , mE FROM App_booths WHERE idBooth=$ID");
    $editaAlertOffline = "<input type='button' class='editButton' onClick='edit(12 , $ID);'>";
    if ($_SESSION['USERTYPE']==6) {
        $editaAlertOffline = "";
    }
    if ($alertOffline) {
        $val = $alertOffline[0]["alertOffline"];
        
        if ($val == 0) {
            echo "<h2>THE OFFLINE ALERT IS <span style='color:red;'>OFF</span> ";
            echo $editaAlertOffline;
            echo "</h2>";
            echo "<p> The offline alert is OFF.</p>";
        } else {
            $hs = $alertOffline[0]["hS"];
            $ms = $alertOffline[0]["mS"];
            $he = $alertOffline[0]["hE"];
            $me = $alertOffline[0]["mE"];

            if ($hs < 10) {
                $hs = "0" . $hs;
            }
            if ($he < 10) {
                $he = "0" . $he;
            }
            if ($ms < 10) {
                $ms = "0" . $ms;
            }
            if ($me < 10) {
                $me = "0" . $me;
            }
            echo "<h2>THE OFFLINE ALERT IS <span style='color:green;'>ON</span> ";
            echo $editaAlertOffline;
            echo "<p>You will receive an alert if your PhotoBooth is offline between $hs:$ms to $he:$me (click edit to chage the timer)</p>";
        }
    }else{
            echo "<h2>THE OFFLINE ALERT IS <span style='color:red;'>OFF</span> ";
            echo $editaAlertOffline;
            echo "</h2>";
    }
} else {
    echo "<h2>Alert Email :</h2>";
    echo "<p> Your Alert email is not defined. Go to your profile and define it.(click edit to chage the timer)</p>";
}
echo "</div>";
?>
