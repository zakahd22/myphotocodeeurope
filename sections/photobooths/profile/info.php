<?php

include '../../../sessio.php';

error_log( "TO_DELETE sections/phoyobooths/profile/info.php 01" );


require_once G_PATH . 'common/conexio.php'; 
$ID = $_POST["id"];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT * FROM App_booths WHERE idBooth=$ID");

if ($CLD_CON->FetchArray()) {
    $idBooth = $CLD_CON->GetArrayField("idBooth");
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $char = $CLD_CON->GetArrayField("type");
    $typeID = $CLD_CON->GetArrayField("CLD_idType");
    $pbname = $CLD_CON->GetArrayField("name");//20150709pbname
    $location = $CLD_CON->GetArrayField("location");
    $latitude = $CLD_CON->GetArrayField("latitude");
    $status = $CLD_CON->GetArrayField("CLD_Status");
    $ownerID = $CLD_CON->GetArrayField("owner");
    $dis = $CLD_CON->GetArrayField("CLD_Distributor");
    $dateOwner = $CLD_CON->GetArrayField("CLD_date_tOwner");
    $dateProduction = $CLD_CON->GetArrayField("CLD_date_production");
    $dateTodistributor = $CLD_CON->GetArrayField("CLD_date_sold");
    $PBtwid = $CLD_CON->GetArrayField("PBtwid");
    $version = $CLD_CON->GetArrayField("version");
    $lastConnLocal = $CLD_CON->GetArrayField("lastConnLocal");

    $CLD_CON2->OpenRs("SELECT `when` FROM App_info WHERE idBooth=$idBooth ORDER BY `when` DESC LIMIT 1");
    if ($CLD_CON2->FetchArray()){
        $when = $CLD_CON2->GetArrayField("when");
    }

    $CLD_CON2->OpenRs("SELECT Appusr_datetime FROM photos WHERE pbs_id=$idBooth ORDER BY Appusr_datetime DESC LIMIT 1");
    if ($CLD_CON2->FetchArray()){
        $photosaved = $CLD_CON2->GetArrayField("Appusr_datetime");
    }

    error_log( "TO_DELETE sections/phoyobooths/profile/info.php dis=$dis;" );
    if(strlen($dis)>0){//20250124PBinfo
    $CLD_CON2->OpenRs("SELECT Name FROM CLD_Distributors WHERE id=$dis");
    if($CLD_CON2->FetchArray()){
        $distributor = $CLD_CON2->GetArrayField("Name");
    }
    }//20250124PBinfo
    
    $CLD_CON2->OpenRs("SELECT name , App_email FROM rentals WHERE id=$ownerID");
    if($CLD_CON2->FetchArray()){
        $OwnerName = $CLD_CON2->GetArrayField("name");
        $OwnerEmail = $CLD_CON2->GetArrayField("App_email");
    }
    
    if (!empty($latitude)) {
        $latitude = $latitude / 1000000;
    } else {
        $latitude = "-";
    }
    $longitude = $CLD_CON->GetArrayField("longitude");
    if (!empty($longitude)) {
        $longitude = $longitude / 1000000;
    } else {
        $longitude = "-";
    }
    $paypal = $CLD_CON->GetArrayField("payPalVendor");
}

if (!empty($typeID)) {
    $CLD_CON2->OpenRs("SELECT name FROM CLD_boothTypes WHERE id=$typeID");
    if ($CLD_CON2->FetchArray()) {
        $typeName = $CLD_CON2->GetArrayField("name");
    }
} else {
    $CLD_CON2->OpenRs("SELECT id, name FROM CLD_boothTypes WHERE `char`='$char' LIMIT 1");
    if ($CLD_CON2->FetchArray()) {
        $typeName = $CLD_CON2->GetArrayField("name");
        $typeID = $CLD_CON2->GetArrayField("id");
    }
}

$CLD_CON2->OpenRs("SELECT idDongle FROM App_boothDongle WHERE idBooth=$idBooth AND datetimeF IS NULL LIMIT 1");
if ($CLD_CON2->FetchArray()) {
    $dongleID = $CLD_CON2->GetArrayField("idDongle");
    $CLD_CON3->OpenRs("SELECT rand_string FROM booths WHERE id=$dongleID");
    if ($CLD_CON3->FetchArray()) {
        $stg = $CLD_CON3->GetArrayField("rand_string");
        $r_string = " - " . $char . $stg;
    }
} else {
    $r_string = "";
    $stg = "";
}
echo "<link rel='stylesheet' href='sections/photobooths/resources/css/info.css' type='text/css'>";
echo "<script src='sections/photobooths/resources/js/info.js'></script>";

echo "<div class='inContent'>";
echo "<div class='boxLeft'>";

echo "<h1>PHOTOBOOTH INFO</h1>";
echo "<div class='box'>";

if($_SESSION['USERTYPE'] == 2  || $_SESSION['USERTYPE'] == 1){
  echo "<div id='manufac_edit' onClick='edit(69 , $ID)'></div>";  
}
echo "<div class='imgProfileBooth'>";
    if(empty($typeID )){
    echo "<img src='images/web/pb/no-machine.png' style='width:80%;max-height:95%;'>";
    }else{
        if(file_exists(G_PATH . "/images/web/pb/$typeID.png")){
            echo "<img src='images/web/pb/$typeID.png' style='width:80%;margin-left:10%;margin-top:10%;max-height:95%;'>";
        }else{
             echo "<img src='images/web/pb/no-machine.png' style='width:80%;max-height:95%;'>";
        }
    }
echo "</div>";
echo "<div class='infoProfileBooth'>";
if ($_SESSION['USERTYPE'] == 1 || $_SESSION['USERTYPE'] == 2 ) {
    echo "<p>ID : $idBooth </p>";    
    echo "<p>S/N : $sn <input type='button' class='editButton' onClick='edit(33 , $ID);'></p>";    
    if(!$PBtwid){
        $PBtwid = "remote support request not done";
    }
    echo "<p>Teamviewer id : $PBtwid <input type='button' class='editButton' onClick='edit(82 , $ID);'></p>";
}elseif ($_SESSION['USERTYPE']==6) {
    echo "<p>ID : $idBooth </p>";    
    echo "<p>S/N : $sn </p>";    
    if(!$PBtwid){
        $PBtwid = "remote support request not done";
    }
    echo "<p>Teamviewer id : $PBtwid </p>";
}



else {
    echo "<p>S/N : $sn  </p>";
}
echo "<p> String : $r_string</p>";
echo "<p>Type : $typeName</p>";
echo "<p>Version : $version</p>";

//20150709pbname INICI
//ok if($ownerID){//tester
echo "<h3>PhotoBooth Name</h3>";
echo "<p>$pbname ";
if ($_SESSION['USERTYPE']<6) {
    echo "<input type='button' class='editButton' onClick='edit(101 , $ID);'>";
}        
echo "</p>";
//ok }//tester
//20150709pbname FINAL


if($_SESSION['USERTYPE']<4){
    if($status==0){
        echo "<p>Status: $BOOTHS_TYPE_STATUS[0] <input type='button' class='editButton' onClick='edit(44 , $ID);'></p>";
    }
     if($status==1){
        echo "<p> Finish Product : $dateProduction</p>"; 
        echo "<p>Status: $BOOTHS_TYPE_STATUS[1] <input type='button' class='editButton' onClick='edit(46 , $ID);'></p>";
    }
     if($status==2){
         echo "<p> Finish Product : $dateProduction</p>"; 
         echo "<p> To distributor : $dateTodistributor</p>"; 
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[2] <input type='button' class='editButton' onClick='edit(47 , $ID);'></p>";
    }
    if($status==3){
         echo "<p> Finish Product : $dateProduction</p>"; 
         echo "<p> To Distributor : $dateTodistributor</p>"; 
        echo "<p> To Owner : $dateOwner</p>";
               echo "<p>Status: $distributor - $BOOTHS_TYPE_STATUS[3] -> <span class='link2' onclick='openLink(\"Owner\" ,$ownerID);'>$OwnerName</span> <input type='button' class='editButton' onClick='edit(55 , $ID);'></p>";

    }   
    if($status==4){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[4] <input type='button' class='editButton' onClick='edit(57 , $ID);'></p>";
    }   
    if($status==5){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[5] <input type='button' class='editButton' onClick='edit(49 , $ID);'></p>";
    }  
      if($status==6){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[6] <input type='button' class='editButton' onClick='edit(49 , $ID);'></p>";
    }   
    if($status==7){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[7] <input type='button' class='editButton' onClick='edit(52 , $ID);'></p>";
    }  
    if($status==8){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[8] <input type='button' class='editButton' onClick='edit(50 , $ID);'></p>";
    }   
}


if($_SESSION['USERTYPE']==6){
    if($status==0){
        echo "<p>Status: $BOOTHS_TYPE_STATUS[0] </p>";
    }
     if($status==1){
        echo "<p> Finish Product : $dateProduction</p>"; 
        echo "<p>Status: $BOOTHS_TYPE_STATUS[1] </p>";
    }
     if($status==2){
         echo "<p> Finish Product : $dateProduction</p>"; 
         echo "<p> To distributor : $dateTodistributor</p>"; 
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[2] </p>";
    }
    if($status==3){
         echo "<p> Finish Product : $dateProduction</p>"; 
         echo "<p> To Distributor : $dateTodistributor</p>"; 
        echo "<p> To Owner : $dateOwner</p>";
               echo "<p>Status: $distributor - $BOOTHS_TYPE_STATUS[3] -> <span class='link2' onclick='openLink(\"Owner\" ,$ownerID);'>$OwnerName</span> </p>";

    }   
    if($status==4){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[4] </p>";
    }   
    if($status==5){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[5] </p>";
    }  
      if($status==6){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[6] </p>";
    }   
    if($status==7){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[7] </p>";
    }  
    if($status==8){
        echo "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[8] </p>";
    }   
}
echo "</div>";
echo "</div>";

/* LOCATION !! */
echo "<h1 style='margin-top: 380px;'>LOCATION</h1>";
echo "<div class='box'>";
echo "<div class='imgProfileBooth'>";
if ($latitude == "-" || $longitude == "-") {
    echo "<img src='images/web/no-map.jpg' style='width:100%;'>";
} else {
    echo "<div id='mp' class='map' style='width:100%;height:260px;'></div>";
    echo "<script>createMap($latitude , $longitude)</script>";
}
echo "</div>";
echo "<div class='infoProfileBooth'>";
echo "<h3>Location Name</h3>";
echo "<p>$location ";
if ($_SESSION['USERTYPE']<6) {
echo "<input type='button' class='editButton' onClick='edit(6 , $ID);'></p>";
}
echo "<h3>GPS Position ";
if ($_SESSION['USERTYPE']<6) {
    echo  "<input type='button' class='editButton' onClick='edit(7 , $ID);'></h3>";
}
echo "<p>Latitude: $latitude</p>";
echo "<p>Longitude : $longitude";
echo "</div>";
echo "</div>";
echo "</div>";


//FI LOCATION!!



echo "<div class='boxRight'>";
echo "<h1 style='margin-left: -5px; margin-top: 30px;'>SETTINGS</h1>";
if (empty($paypal)) {
    echo "<h1>Paypal is <span style='color:red;'>OFF</span>";
    if ($_SESSION['USERTYPE']<6) {
    echo "<input type='button' class='editButton'  onClick='edit(8 , $ID);'>";
     
    } 
    echo "</h1>";
} else {
    echo "<h1>Paypal is <span style='color:green;'>ON</span>";
    if ($_SESSION['USERTYPE']<6) {
    echo "<input type='button' class='editButton'  onClick='edit(8 , $ID);'>";
    }
    echo "</h1>";
}

echo "<div class='box_nomargin'>";
//echo "<p>The Merchant account ID was developed as a substitute for your email address to prevent spam bots from harvesting your email address on web site pages that contain your item button code.  The Merchant account ID is sometimes referred to as your PayPal Account ID Number.</p> ";
echo "<p> Paypal account : ";
if (empty($paypal)) {
    echo "No defined";
} else {
    echo $paypal;
}
echo "</p>";
echo "</div>";

/*echo "<p>This option will keep you informed about the PhotoBooth. By activating this option, you will receive weekly,
     monthly and yearly emails along with a report of the PhotoBooth's activity (sales, cash, stock, etc...). 
    You will receive this email to the email address that you have provided  in your profile under Email Alerts.Click Active to activatie this option.</p>";*/
$CLD_CON->OpenRs("SELECT value FROM App_boothConfigDef WHERE idBooth = $ID AND typeConfig = 1");
if ($CLD_CON->GetRsRows() == 0) {
    $value = "NO";
} else {
    while ($CLD_CON->FetchArray()) {
        $value = $CLD_CON->GetArrayField("value");
    }
}
echo "<div class='box_nomargin'>";
if ($value == "NO") {
    echo "<h1>Email reports are <span style='color:red;'>OFF</span>";
    if ($_SESSION['USERTYPE']<6) {
    echo "<input type='button' class='editButton' onClick='edit(9 , $ID);'>";
    }
    echo "</h1>";
    echo "<p> The email reports option is OFF for this PhotoBooth. </p>";
}
if ($value == "YES") {
    echo "<h1>Email reports are <span style='color:green;'>ON</span>";
    if ($_SESSION['USERTYPE']<6) {
    echo "<input type='button' class='editButton' onClick='edit(9 , $ID);'>";
    }
    echo "</h1>";   
    echo "<p> The email reports option is ON for this PhotoBooth. The email will be sent to $OwnerEmail </p>";
}
if($_SESSION['USERTYPE']==1){
//echo "<div class='box'>";
//echo "<h1>PANIC INSTRUCCTIONS <input type='button' class='miniDownload' onClick='panic($ID , $ownerID , \"$sn\");'></h1>";
//echo "<p>List different Qr codes that link to the question of support to solve different errors.</p>";
//echo "<span id='QR'></span>";
//echo "</div>";
}
echo "</div>";
echo "<br>";
/*
 * Afegim info adicional
 */


echo "<h1 style='margin-top: 150px; margin-left: -5px;'>ACTIVITY</h1>";

echo "<div class='box_nomargin'>";
echo "<h3 style='margin-top: 40px;'>Last connection</h3>";
echo "<p>$lastConnLocal</p>";
echo "</div>";

echo "<div class='box_nomargin'>";
echo "<h3>Last data saved</h3>";
echo "<p>$when</p>";
echo "</div>";

echo "<div class='box_nomargin'>";
echo "<h3>Last photo saved</h3>";
echo "<p>$photosaved</p>";
echo "</div>";

echo "</div>";

echo "</div>";



echo "</div>";
?>
<script>
    function panic(id , owner , sn){
        var ajaxData = {id: id , owner:owner , sn:sn};
        $.ajax({
        url: 'sections/photobooths/functions/panicInstrucctions.php',
        type: 'POST',
        success: function(data) {
             
            // $("#QR").html(data);
        window.open(data , '_blank');
        },
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
    }
</script>
