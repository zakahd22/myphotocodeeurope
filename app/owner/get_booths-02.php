<?php
require("common.php");


if(!$APP_user) return;

//<name>".utf8_encode('NG – Zoo – AJ3')."</name>
//<name>".utf8_encode('NG – Zoo – X4E')."</name>



//$str = "NG – Zoo – AJ3";
//Proves d'accès de l'App INICI
//echo "$APP_xml
//<booth>
//<id>23</id>
//<name>NG – Zoo – AJ3</name>
//<status>Error</status>
//<location>
//<latitude>1.23456</latitude>
//<longitude>9.87654</longitude>
//</location>
//</booth>
//<booth>
//<id>24</id>
//<name>NG – Zoo – X4E</name>
//<status>OK</status>
//<location>
//<latitude>1.23456</latitude>
//<longitude>9.87654</longitude>
//</location>
//</booth>
//</return>
//";
//return;

//Proves d'accès de l'App FINAL

//Params
////$idBooth = "";
////if(isset($_REQUEST['id'])){$idBooth = $_REQUEST['id']; }


//taula App_booths SELECT `idBooth`,`type`,`owner`,`name`,`obs`,`serialnumber`,`location`,`latitude`,`longitude`, FROM `App_booths` WHERE 1
//taula App_BoothDongle SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_BoothDongle` WHERE 1
//taula booths SELECT `id`, `dongle`, `reference`, `rand_string`, `rental_id` FROM `booths` WHERE 1
//taula booth_types SELECT `id`, `char`, `name`, `logo_w`, `logo_h`, `frames_w`, `frames_h`, `welcome_w`, `welcome_h`, `banner_w`, `banner_h`, `custom_w`, `custom_h`, `screens` FROM `booth_types` WHERE 1

//no farem subqueries
$sql = "SELECT idBooth, booth_types.name, App_booths.name, 'OK' AS status, App_booths.location, App_booths.latitude, App_booths.longitude
    FROM App_booths LEFT JOIN booth_types ON App_booths.type = booth_types.char
    WHERE owner='$APP_userId'; ";

echo "TRACE 02 $sql"; return;


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<status>Error - Database error code: 0002a </status></return>";
return;
}
$nBooths = 0;
while($APP_BdD->FetchRs()){

    $array_idBooth[$nBooths] = $APP_BdD->GetField(1);
    $array_type[$nBooths] = APP_preparaXML($APP_BdD->GetField(2));
    $array_name[$nBooths] = APP_preparaXML($APP_BdD->GetField(3));
    $array_status[$nBooths] = $APP_BdD->GetField(4);
    $array_location[$nBooths] = APP_preparaXML($APP_BdD->GetField(5));
    $array_latitude[$nBooths] = $APP_BdD->GetField(6);
    $array_longitude[$nBooths] = $APP_BdD->GetField(7);
    $nBooths++;
}
$APP_BdD->CloseRs();
$xml.= "<status>OK</status>";

for($i = 0; $i<$nBooths; $i++){
    $xml.= "<booth>";
    $xml.= "<id>$array_idBooth[$i]</id>";
    $xml.= "<type>$array_type[$i]</type>";
    $xml.= "<name>$array_name[$i]</name>";
    
    $sql = "SELECT booths.rand_string FROM `App_BoothDongle` INNER JOIN booths ON App_BoothDongle.idDongle = booths.id 
        WHERE App_BoothDongle.idBooth = '$APP_userId' ORDER BY datetimeS DESC";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<status>Error - Database error code: 0002b $i</status></return>";
    return;
    }
    if($APP_BdD->FetchRs()){
        $tmp = $APP_BdD->GetField(1);
        $xml.= "<code>$tmp</code>";
    }
    else{
        $xml.= "<code></code>";
    }
    $APP_BdD->CloseRs();
    $xml.= "<status>$array_status[$i]</status>";
    $xml.= "<location>$array_locations[$i]</location>";
    $xml.= "<latitude>$array_latitude[$i]</latitude>";
    $xml.= "<longitude>$array_longitude[$i]</longitude>";

    $xml.= "</booth>";
}


echo "$APP_xml$xml</return>"; // no cal res més

//////Merda, no van els subqueries en  els JOIN INICI
////
////
////$myBoothDongle = " (SELECT DISTINCT `idBooth`, `idDongle`, booths.`rand_string`
////    FROM `App_BoothDongle` INNER JOIN booths ON App_BoothDongle.idDongle = booths.id 
////    WHERE rental_id = '$APP_userId' ORDER BY datetimeS DESC) AS myBoothDongle";
////
////
//////NOTA: de moment totes OK
////$sql = "SELECT idBooth, booth_types.name, App_booths.name, booths.rand_string, 'OK' AS status, App_booths.latitude, App_booths.longitude
////    FROM (App_booths LEFT JOIN booth_types ON App_booths.type = booth_types.char)
////    LEFT JOIN $myBoothDongle ON App_booths.idBooth = myBoothDongle.idBooth
////    WHERE owner='$APP_userId'; ";
////$esOK = $APP_BdD->OpenRs($sql);
////if(!$esOK){
//////caldria controlar l'error
////echo "$APP_xml<status>Error - Database error code: 0002a </status></return>";
////return;
////}
////$xml.= "<status>OK</status>";
////while($APP_BdD->FetchRs()){
////    $xml.= "<booth>";
////    $idBooth =  $APP_BdD->GetField(1);
////    $xml.= "<id>$idBooth</id>";
////    $tmp = $APP_BdD->GetField(2);
////    $xml.= "<type>$tmp</type>";
////    $tmp = APP_preparaXML($APP_BdD->GetField(3));
////    $xml.= "<name>$tmp</name>";
////    $tmp = $APP_BdD->GetField(4);
////    $xml.= "<code>$tmp</code>";
////    $tmp = $APP_BdD->GetField(5);
////    $xml.= "<status>$tmp</status>";
////    
////    $xml.= "<location>";
////    $tmp = $APP_BdD->GetField(6);
////    $xml.= "<latitude>$tmp</latitude>";
////    $tmp = $APP_BdD->GetField(7);
////    $xml.= "<longitude>$tmp</longitude>";
////    $xml.= "</location>";
////
////
////    $APP_xml.= "</booth>";
////
////}
////$APP_BdD->CloseRs();
////
////
////
////
//////Merda, no van els subqueries en  els JOIN FINAL


?>
