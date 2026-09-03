<?php
require("common.php");
if(!$APP_user) return;

//definicions d'alertes per a un PhotoBooth a editar
//info de les condicions d'alerta no editables des de App

if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
else{
echo "$APP_xml<comm_status>$APPERROR_noid</comm_status></return>";
return;
}
$xml = $APP_xmlOKcomm;


//dades del booth i info per a d'altres alertes
//<booth_type>NG</booth_type>
//<booth_name>Zoo</booth_name>
//<booth_code>AJ3</booth_code>
//SELECT `estat`, `type`, `owner`, `name`,`hS`, `mS`, `hE`, `mE` FROM `App_booths` WHERE 1


//20140108 INICI (veure //20130926 a get_booths)

//$myBoothDongle = " (SELECT DISTINCT `idBooth`, `idDongle`, booths.`rand_string`
//    FROM `App_boothDongle` INNER JOIN booths ON App_boothDongle.idDongle = booths.id
//    WHERE idBooth = $idBooth ORDER BY datetimeS DESC) AS myBoothDongle";
$myBoothDongle = " (SELECT DISTINCT `idBooth`, `idDongle`, booths.`rand_string`
    FROM `App_boothDongle` INNER JOIN booths ON App_boothDongle.idDongle = booths.id
    WHERE idBooth = $idBooth AND `datetimeF` IS NULL ORDER BY datetimeS DESC) AS myBoothDongle";

//20140108 FINAL

$sql = "SELECT booth_types.name, App_booths.name, myBoothDongle.rand_string, `estat` 
    FROM (App_booths LEFT JOIN booth_types ON App_booths.type = booth_types.`char`)
    LEFT JOIN $myBoothDongle ON App_booths.idBooth = myBoothDongle.idBooth
    WHERE App_booths.`idBooth`=$idBooth; ";


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

$xml.= "<booth>";
$xml.= "<booth_id>$idBooth</booth_id>";
if($APP_BdD->FetchRs()){
    $tmp =  APP_preparaXML($APP_BdD->GetField(1));
    $xml.= "<booth_type>$tmp</booth_type>";
    $tmp = APP_preparaXML($APP_BdD->GetField(2));
    $xml.= "<booth_name>$tmp</booth_name>";
    $tmp = $APP_BdD->GetField(3);
    $xml.= "<booth_code>$tmp</booth_code>";
    $tmp = $APP_BdD->GetField(4);
    $xml.= "<booth_status>{$APP_estatsBooth[$tmp]}</booth_status>";
}
$APP_BdD->CloseRs();

//201209 $xml.= "</booth>";


//SELECT `id`, `idBooth`, `typeAlert`, `when`, `estat` FROM `App_boothAlert` WHERE `idBooth` = 1 AND `estat` < 2
////SELECT `typeAlert` , `label` , `values` , `textAlert` FROM `App_alerts` 

//alertes
//alertes actives

$sql = "SELECT `when`,`textAlert` FROM `App_boothAlert`
    LEFT JOIN `App_alerts` ON `App_boothAlert`.`typeAlert` = `App_alerts`.`typeAlert`
    WHERE `idBooth`=$idBooth AND `estat`<2 ORDER BY `when` DESC; ";

//echo "TRACE 01 $sql";
//return;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
return;
}
$xml.= "<alerts>"; 
//    $xml.= "<trace>$sql</trace>";
while($APP_BdD->FetchRs()){
    
    $xml.= "<alert>";
    $tmp = $APP_BdD->GetFieldDateTime(1);
    if($tmp){
     $xml.= $tmp->format("m-d-Y H:i ");
    }
    
    $tmp = $APP_BdD->GetField(2);
    $xml.= "$tmp</alert>";
}    

$APP_BdD->CloseRs();

$xml.= "</alerts>";

//20140108 INICI (versió també de play)
$sql = "SELECT `str1` FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo` IN (10,20) AND `str1` IS NOT NULL  ORDER BY `when` DESC  LIMIT 0 , 1 ; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0003.1 </comm_status></return>";
return;
}
$dateReport = "";
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<software>$tmp</software>";
}
else{
    $xml.= "<software></software>";
    
}
$APP_BdD->CloseRs();
//20140108 FINAL


//last report
//SELECT `idInfo` , `when` , `idBooth` , `idDongle` , `typeInfo` , `money` , `currency` , `stock` , `i1` , `i2` , `i3` , `str1` , `str2`
//LIMIT 0 , 1 
$sql = "SELECT `str1`, `when`, `i1` FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=20  ORDER BY `when` DESC  LIMIT 0 , 1 ; ";

//echo "TRACE 02 $sql";
//return;


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0003 </comm_status></return>";
return;
}
$dateReport = "";
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
//20140108    $xml.= "<software>$tmp</software>";
    $dateReport = $APP_BdD->GetFieldDateTime(2);
    $xml.= "<dateReport>";
    if($dateReport){
     $xml.= $dateReport->format("m-d-Y H:i ");
    }
    $xml.= "</dateReport>";
    $tmp =  $APP_BdD->GetField(3);
    $xml.= "<reportnumber>$tmp</reportnumber>";
}
else{
//20120919    $xml.= "<software/><dateReport/>";
//20140108    $xml.= "<software></software><dateReport></dateReport>";//20120919
    $xml.= "<dateReport></dateReport>";//20140108//20120919
    
    
}
$APP_BdD->CloseRs();

    
//20140331 INICI    
//si no s'ha fet cap report, no tenim cap data, agafarem per a fer càlculs de dies i comptadors la data de la primera info
if(!$dateReport){
    $sql = "SELECT `when` FROM `App_info` WHERE `idBooth`=$idBooth ORDER BY `when` LIMIT 0 , 1 ; ";    
    $esOK = $APP_BdD->OpenRs($sql);
    if($esOK){
        if($APP_BdD->FetchRs()){
           $dateReport = $APP_BdD->GetFieldDateTime(1);
        }        
    }

}
//20140331 FINAL    





//lastCheck

//NOTA: el canviaré per una consulta a session (per a alive)

$sql = "SELECT `when` FROM `App_info`
    WHERE `idBooth`=$idBooth  ORDER BY `when` DESC  LIMIT 0 , 1 ; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0004 </comm_status></return>";
return;
}
$dateCheck = "";
if($APP_BdD->FetchRs()){
   $dateCheck = $APP_BdD->GetFieldDateTime(1);
    $xml.= "<dateCheck>";
    if($dateCheck){
     $xml.= $dateCheck->format("m-d-Y H:i ");
    }
    $xml.= "</dateCheck>";
}
else{
//20120919    $xml.= "<dateCheck/>";
    $xml.= "<dateCheck></dateCheck>";//20120919
    
}
$APP_BdD->CloseRs();



//dies entre lastReport i lastCheck
//DateInterval date_diff ( DateTime $datetime1 , DateTime $datetime2 [, bool $absolute = false ] )
//$interval = $datetime1->diff($datetime2);
//echo $interval->format('%R%a days');

//echo "TRACE 03 dateReport: $dateReport i dateCheck : $dateCheck";
//return;

    $xml.= "<days>";
    if($dateReport != "" && $dateCheck != ""){
    // $interval = $dateCheck->diff($dateReport);
    // $xml.= $interval->format('%R%a');
        $xml.= floor(abs($dateCheck->format('U') - $dateReport->format('U')) / (60*60*24));
    }
    else{
        $dateReport  = new DateTime("now");
        $dateCheck = $dateReport;
    }
    $xml.= "</days>";
//

//dades entre lastReport i lastCheck
//SELECT `idInfo` , `when` , `idBooth` , `idDongle` , `typeInfo` , `money` , `currency` , `stock` , `i1` , `i2` , `i3` , `str1` , `str2`
//myDateTimeSerial(DateTime $quan,$top = 0)
    
//plays i money
$sql = "SELECT COUNT(*) AS tDays, SUM(`money`) AS tMoney FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=10 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)}  ; ";

    

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0005 </comm_status></return>";
return;
}
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<plays>$tmp</plays>";
    $tmp =  $APP_BdD->GetField(2);
    $xml.= "<money>$tmp</money>";
}
$APP_BdD->CloseRs();

//stock: darrera informació rebuda
$sql = "SELECT `stock` FROM `App_info`
    WHERE `idBooth`=$idBooth AND `stock` IS NOT NULL ORDER BY `when` DESC   LIMIT 0 , 1 ; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0005b </comm_status></return>";
return;
}
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<stock>$tmp</stock>";
}
$APP_BdD->CloseRs();

//errors printer
$sql = "SELECT COUNT(*) AS err FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=40 AND `i1`=1 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)}  ; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0006 </comm_status></return>";
return;
}
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<errorsPrinter>$tmp</errorsPrinter>";
}
$APP_BdD->CloseRs();

////TRACE!!!!!!!!!!!!!!!!
//echo "$APP_xml$xml</return>"; return;

//errorsPaper
$sql = "SELECT COUNT(*) AS err FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=40 AND `i1`=2 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)}  ; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0007 </comm_status></return>";
return;
}
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<errorsPaper>$tmp</errorsPaper>";
}
$APP_BdD->CloseRs();


//errorsBoard
$sql = "SELECT COUNT(*) AS err FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=40 AND `i1`=3 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)}  ; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0008 </comm_status></return>";
return;
}
if($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<errorsBoard>$tmp</errorsBoard>";
}
$APP_BdD->CloseRs();

//20130430rep INICI no cam errors
//errorsCam
//$sql = "SELECT COUNT(*) AS err FROM `App_info`
//    WHERE `idBooth`=$idBooth AND `typeInfo`=40 AND `i1`=4 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)}  ; ";
//
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
////caldria controlar l'error
//echo "$APP_xml<comm_status>Error Database error code: 0009 </comm_status></return>";
//return;
//}
//if($APP_BdD->FetchRs()){
//    $tmp =  $APP_BdD->GetField(1);
//    $xml.= "<errorsCam>$tmp</errorsCam>";
//}
//$APP_BdD->CloseRs();
//20130430rep FINAL no cam errors


//products

//SELECT `idInfo` , `when` , `idBooth` , `idDongle` , `typeInfo` , `money` , `currency` , `stock` , `i1` , `i2` , `i3` , `str1` , `str2`
//SELECT `id`, `label`, `descr` FROM `App_products` WHERE 1

$sql = "SELECT `label`,COUNT(`idInfo`) FROM `App_info`
    LEFT JOIN `App_products` ON `App_info`.`i1` = `App_products`.`id`
    WHERE `idBooth`=$idBooth AND `typeInfo`=10 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)} 
    GROUP BY `App_info`.`i1` ORDER BY `label`;";


//echo "TRACE 01 $sql";
//return;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0010 </comm_status></return>";
return;
}
$xml.= "<products>"; 
//    $xml.= "<trace>$sql</trace>";
while($APP_BdD->FetchRs()){
//<products>
//<product>
//<name>B/W</name><quantity>n</quantity>
//</product>
    
    $xml.= "<product>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<name>$tmp</name>";
    $tmp = $APP_BdD->GetField(2);
    $xml.= "<quantity>$tmp</quantity>";
    $xml.= "</product>";
}    

$APP_BdD->CloseRs();

$xml.= "</products>";

//sessions INICI

//SELECT `id`, `idBooth`, `idDongle`, `start`, `last` FROM `App_sessions` WHERE 1

//AND `typeInfo`=10 AND `when` >= {$APP_BdD->myDateTimeSerial($dateReport)} AND  `when` <= {$APP_BdD->myDateTimeSerial($dateCheck)} 

$sql = "SELECT `start`, `last` FROM `App_sessions` WHERE `idBooth`=$idBooth  AND `start` >= {$APP_BdD->myDateTimeSerial($dateReport)}
     ORDER BY `start` DESC;";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0010 </comm_status></return>";
return;
}

$nSessions = 0;

$xml.= "<sessions>"; 
//    $xml.= "<trace>$sql</trace>";
while($APP_BdD->FetchRs()){
    
    $array_start[$nSessions] = $APP_BdD->GetFieldDateTime(1);    
    $array_last[$nSessions] = $APP_BdD->GetFieldDateTime(2);    
    
    $nSessions++;
}
$APP_BdD->CloseRs();



for($i = 0; $i<$nSessions; $i++){
//<session>
//<period>mm-dd-yy hh:mm - mm-dd-yy hh:mm</period>
//<plays>n</plays>
//<money>n</money>
//<stock>n</stock>
//</session>

//20120919 NOTA: tots els tgas child de session tindran session_ de prefix

    $xml.= "<session>";
    $xml.= "<session_period>";
    $xml.= $array_start[$i]->format("m-d-Y H:i - ");
    $xml.= $array_last[$i]->format("m-d-Y H:i");
    $xml.= "</session_period>";

    //plays i money
    $sql = "SELECT COUNT(*) AS tPlays, SUM(`money`) AS tMoney FROM `App_info`
        WHERE `idBooth`=$idBooth AND `typeInfo`=10 AND `when` >= {$APP_BdD->myDateTimeSerial($array_start[$i])} AND  `when` <= {$APP_BdD->myDateTimeSerial($array_last[$i])}  ; ";


//$xml.= "<trace>$sql</trace>";
//echo "$APP_xml$xml</return>"; // no cal res més
//
//// var_dump($array_start);
////var_dump($array_last);
// ////
////
//return;

    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0010b </comm_status></return>";
    return;
    }
    if($APP_BdD->FetchRs()){
        $tmp =  $APP_BdD->GetField(1);
        $xml.= "<session_plays>$tmp</session_plays>";
        $tmp =  $APP_BdD->GetField(2);
        $xml.= "<session_money>$tmp</session_money>";
    }
    $APP_BdD->CloseRs();


    //stock
    $sql = "SELECT `stock` FROM `App_info`
        WHERE `idBooth`=$idBooth AND `typeInfo`=10 AND `when` >= {$APP_BdD->myDateTimeSerial($array_start[$i])} AND  `when` <= {$APP_BdD->myDateTimeSerial($array_last[$i])} ORDER BY `when` DESC LIMIT 0,1  ; ";

    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0010c </comm_status></return>";
    return;
    }
    $xml.= "<session_stock>";
    if($APP_BdD->FetchRs()){
        $tmp =  $APP_BdD->GetField(1);
        $xml.= $tmp;
    }
    $APP_BdD->CloseRs();
    $xml.= "</session_stock>";

    $xml.= "</session>";

}

$xml.= "</sessions>"; 

//<sessions>
//<session>
//<period>mm-dd-yy hh:mm - mm-dd-yy hh:mm</period>
//<plays>n</plays>
//<money>n</money>
//<stock>n</stock>
//</session>


//sessions FINAL


//history

//SELECT `idInfo` , `when` , `idBooth` , `idDongle` , `typeInfo` , `money` , `currency` , `stock` , `i1` , `i2` , `i3` , `str1` , `str2`


//20140108 INICI
//$sql = "SELECT `when`, `i1`, `i2`, `money` FROM `App_info`
//    WHERE `idBooth`=$idBooth AND `typeInfo`=20  ORDER BY `when` DESC; ";
$sql = "SELECT `when`, `i1`, `i2`, `i3` FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=20  ORDER BY `when` DESC; ";

//20140108 FINAL

//echo "TRACE 01 $sql";
//return;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0012 </comm_status></return>";
return;
}
$xml.= "<reports>"; 
//    $xml.= "<trace>$sql</trace>";

//20120919 NOTA: tots els tgas child de report tindran report_ de prefix


$totalDays = 0;
$totalPlays = 0;
$totalMoney = 0;

$xmlAbansDies = "";
$xmlDespresDies = "";

$nReports = 0;

while($APP_BdD->FetchRs()){
//<reports>
//<report>
//<dateReport>mm-dd-yy</dateReport>
//<days>n</days>
//<reportnumber>n</reportnumber>
//<plays>n</plays>
//<money>n</money>
//</report>
    
    //primer la dat a i el càlcul de dies a partir del segon, ens hem guardat xml del report anterior
    $dateReport = $APP_BdD->GetFieldDateTime(1);
    if($nReports){//només si ja en teniem un
        $xml.= "$xmlAbansDies<report_days>";
        if($dateReport && $dateCheck){
        // $interval = $dateCheck->diff($dateReport);
        // $xml.= $interval->format('%R%a');
            $nDays = floor(abs($dateCheck->format('U') - $dateReport->format('U')) / (60*60*24));
            $totalDays+=$nDays;
            $xml.= $nDays;
        }
        $xml.= "</report_days>$xmlDespresDies";
    }
    $nReports++;
    $dateCheck = $dateReport;
    
    $xmlAbansDies = "<report>";
//    $dateReport = $APP_BdD->GetFieldDateTime(1);
    $xmlAbansDies.= "<report_date>";
    if($dateReport){
     $xmlAbansDies.= $dateReport->format("m-d-Y");
    }
    $xmlAbansDies.= "</report_date>";
    $tmp =  $APP_BdD->GetField(2);
    $xmlAbansDies.= "<report_number>$tmp</report_number>";
    
    $tmp = $APP_BdD->GetField(3);
    if($tmp) $totalPlays+= $tmp;
    $xmlDespresDies = "<report_plays>$tmp</report_plays>";
    $tmp = $APP_BdD->GetField(4);
    if($tmp) $totalMoney+= $tmp;
    $xmlDespresDies.= "<report_money>$tmp</report_money>";
    $xmlDespresDies.= "</report>";
}    

$APP_BdD->CloseRs();


//20120919 $xml.= "$xmlAbansDies<days/>$xmlDespresDies";
$xml.= "$xmlAbansDies<report_days></report_days>$xmlDespresDies";//20120919

//20120919 NOTA: tots els tgas child de total tindran total_ de prefix

$xml.= "<total>";
//20120919 if($totalDays) $xml.= "<days>$totalDays</days>"; else $xml.= "<days/>";
if($totalDays) $xml.= "<total_days>$totalDays</total_days>"; else $xml.= "<total_days></total_days>";//20120919
$xml.= "<total_plays>$totalPlays</total_plays>";
$xml.= "<total_money>$totalMoney</total_money>";
$xml.= "</total>";

$xml.= "</reports>";

 $xml.= "</booth>";//201209

echo "$APP_xml$xml</return>"; // no cal res més



?>
