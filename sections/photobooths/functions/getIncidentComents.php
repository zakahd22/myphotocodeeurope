<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

$ID = $_POST['id'];
echo "<h1>More Comments of Incident :  <input type='button' class='miniAdd' onclick='edit(39 , $ID);'></h1>";
$CLD_CON->OpenRs("SELECT * FROM CLD_Incidents WHERE id=$ID");
if($CLD_CON->FetchArray()){
    $in_id = $CLD_CON->GetArrayField("id");
    $coment = stripslashes($CLD_CON->GetArrayField("coment"));
    $date = $CLD_CON->GetArrayField("datetime");
    $code = $CLD_CON->GetArrayField("code");
    $user = $CLD_CON->GetArrayField("user");
    $status = $CLD_CON->GetArrayField("status");
    
    echo "<div class='incReg' style='cursor:auto;overflow:auto;'>";
    echo "<p style='margin-top:0px;margin-bottom:10px;border-bottom:1px solid white;' >";
    echo "$code , $date";

    echo "<span style='width:45%;margin-right:4%;float:right;text-align:right;'>";
    echo "$user";
    echo "</span>";
    echo "</p>";
    echo "<p style='margin-top:5px;margin-bottom:5px;margin-left:4%;margin-right:5%;'>";
    if(strlen($coment)>150){
        $coment = substr( $coment , 0 , 150) . "...";
    }
    echo $coment;
    
    
    echo "</p>";
    echo "<p style='margin-top:10px;margin-bottom:0px;text-align:right;border-top:1px solid white;'>";
    
    if($status == 0){
        echo "Not Solved";
    }
    if($status == 1){
        echo "Seen by SuperUser";
    }   
    if($status == 2){
        echo "Solved";
    }

    echo "</p>";    
    echo "</div>";
    
}

echo "<hr>";
echo "<div style='width:100%;'>"; 
$CLD_CON->OpenRs("SELECT * FROM CLD_Inc_coments WHERE incident=$ID ");
if($CLD_CON->GetRsRows() ==0){
    echo "<p>No more coments</p>";
}
while($CLD_CON->FetchArray()){
    $date = $CLD_CON->GetArrayField("datetime");
    $user = $CLD_CON->GetArrayField("user");
    $coment = stripslashes($CLD_CON->GetArrayField("coment"));
    
    echo "<div style='overflow:hidden;width:90%;margin-left:3%;padding:2%; border-bottom:1px solid gray;'>";
    echo "<p>$date <span style='float:right;'>$user</span></p>";
    echo "<p>$coment</p>";
    echo "</div>";
    
}
echo "</div><hr>";
?>
