<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

$ID = $_POST["id"];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT typeAlert , textAlert FROM App_alerts");
while($CLD_CON->FetchArray()){
    $t = $CLD_CON->GetArrayField("typeAlert");
    $a_Text =  $CLD_CON->GetArrayField("textAlert");
    $types[$t] = $a_Text;
}


echo "<div class='inContent'>";
$CLD_CON->OpenRS("SELECT * FROM App_boothAlert x WHERE x.estat<2 AND x.idBooth=$ID ORDER BY x.when DESC");
echo "<h1>Unsoveld Alerts:</h1>";
echo "<div class='noSolvedAlert'>";

if($CLD_CON->GetRsRows() == 0 ){
     echo "This Photobooth is OK";
}
while($CLD_CON->FetchArray()){
    $i = $CLD_CON->GetArrayField("typeAlert");
    $when = $CLD_CON->GetArrayField("when");    
    echo "<p>$types[$i]<span style='float:right;display:inline;'>$when</span></p>";
}

echo "</div>";

$CLD_CON->OpenRS("SELECT * FROM App_boothAlert x WHERE x.estat=2 AND x.idBooth=$ID ORDER BY x.when DESC");
  echo "<h1>Soveld Alerts:</h1>";
  echo "<div class='solvedAlert'>";

while($CLD_CON->FetchArray()){
    $i = $CLD_CON->GetArrayField("typeAlert");
    $when = $CLD_CON->GetArrayField("when");
    
    echo "<p>$types[$i]<span style='float:right;display:inline;'>$when</span></p>";
}
echo "</div>";
echo "<div class='alertsPhotobooth'>";

$CLD_CON2->OpenRs("SELECT r.App_email FROM rentals r LEFT JOIN App_booths b ON b.owner=r.id WHERE b.idBooth=$ID");
if($CLD_CON2->FetchArray()){
    $emailAlerts = $CLD_CON2->GetArrayField("App_email");

    echo "<h2>Alert Email :</h2>";
    echo "<p> Your Alert email is $emailAlerts. (Set it in your profile)";
    echo "<h2> Film Alert ";
    echo "<input type='button' class='editButton' onClick='edit(10 , $ID);'>";
    echo "</h2>";
    $CLD_CON->OpenRs("SELECT value FROM App_boothAlertDef WHERE idBooth=$ID AND typeAlert=11");
    if($CLD_CON->FetchArray()){
        $val = $CLD_CON->GetArrayField("value");
        if($val == "none"){
             echo "<p>The Film Alert is not active.</p>";
        }else{
            echo "<p>When the film stock falls below $val units, you will receive an alert.</p>";
        }
    }else{
        echo "<p>The Film Alert is not active.</p>";
    }
    echo "<h2> Money Alert ";
    echo "<input type='button' class='editButton' onClick='edit(11 , $ID);'>";
    echo "</h2>";
    $CLD_CON->OpenRs("SELECT value FROM App_boothAlertDef WHERE idBooth=$ID AND typeAlert=12");
    if($CLD_CON->FetchArray()){
        $val = $CLD_CON->GetArrayField("value");
        if($val == "none"){
             echo "<p>The money alert is not active./p>";
        }else{
            echo "<p>When the cash box reached $val $/€/&pound;/kr... , you will receive an alert. </p>";
        }
    }else{
        echo "<p>The money alert is not active.</p>";
    }

    echo "<h2> Offline Alert ";
    echo "<input type='button' class='editButton' onClick='edit(12 , $ID);'>";
    echo "</h2>";
    $CLD_CON->OpenRs("SELECT timeZone, alertOffline, hS , mS ,hE , mE FROM App_booths WHERE idBooth=$ID");
    if($CLD_CON->FetchArray()){
        $val = $CLD_CON->GetArrayField("alertOffline");
        if($val == 0){
             echo "<p> The offline alert is not active.</p>";
        }else{
            $hs = $CLD_CON->GetArrayField("hS");
            $ms = $CLD_CON->GetArrayField("mS");
            $he = $CLD_CON->GetArrayField("hE");
            $me = $CLD_CON->GetArrayField("mE");
            
            if($hs<10){
                $hs = "0".$hs;
            }
            if($he<10){
                $he = "0".$he;
            }
            if($ms<10){
                $ms = "0".$ms;
            }
            if($me<10){
                $me = "0".$me;
            }
            echo "<p>You will receive an alert if your PhotoBooth is offline between $hs:$ms to $he:$me </p>";
           
        }
    }
}else{
    echo "<h2>Alert Email :</h2>";
    echo "<p> Your Alert email is not defined. Go to your profile and define it.";
}
echo "</div>";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
