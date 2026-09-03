<?php
/*
 * Report després de Collection, ens crida PB_Report.php
 */

//20150626   $sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email,booth_types.`name`,App_booths.`serialnumber` FROM (`App_booths` JOIN rentals ON App_booths.owner=rentals.id) ";
//20220124 $sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email,booth_types.`name`,App_booths.`serialnumber` ";//Això ha canviat. Ara: CLD_idType  igual a id de CLD_boothTypes
$sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email,CLD_boothTypes.`name`,App_booths.`serialnumber` ";
$sql.=", `App_booths`.cardReaderSN ";//20150626
$sql.=", `App_booths`.CLD_date_tOwner ";//20150709dateOwner
$sql.="FROM (`App_booths` JOIN rentals ON App_booths.owner=rentals.id) ";//20150626

//20220124 $sql.=" JOIN booth_types ON App_booths.`type` = booth_types.`char` WHERE idBooth = $APP_idBooth;"; //Això ha canviat. Ara: CLD_idType  igual a id de CLD_boothTypes
$sql.=" JOIN CLD_boothTypes ON App_booths.`CLD_idType` = CLD_boothTypes.`id` WHERE idBooth = $APP_idBooth;";
     

APP_fesLogDebbug("Repdc_collection TRACE01 sql:$sql","logDebug20170220");



$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "Error Database report collection, error code: 0001 $sql";
return;
}

//echo "#TRACE $sql";//a eliminar
//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar


$nBooths = 0;
if($APP_BdD->FetchRs()){
    $idBooth = $APP_BdD->GetField(1); 
    $name    = $APP_BdD->GetField(2); 
    $location= $APP_BdD->GetField(3); 
    $version = $APP_BdD->GetField(4); 
    $owner   = $APP_BdD->GetField(5); 
    $mail    = $APP_BdD->GetField(6); 
    $type    = $APP_BdD->GetField(7); 
    $sn      = $APP_BdD->GetField(8); 
    $ccsn    = $APP_BdD->GetField(9); //20150626
    $dataMin = $APP_BdD->GetFieldDateTime(10);//20150709dateOwner
    $APP_BdD->CloseRs();
}
else{
//echo "#TRACE no fetch!!!! ";//a eliminar
    $APP_BdD->CloseRs();
    return;
}

if(!$mail) return;

//echo "#TRACE mail: $mail ";  //a eliminar    





$htmlBegin = "<h1>DC PhotoBooth Report</h1>";

//busquem la data del PB_Report anterior (o la primera info)
//20170612repCash  $sql = "SELECT `when`, `i1` FROM `App_info`
//20170627repCash $sql = "SELECT `when`,`i3`, `i2` FROM `App_info`
$sql = "SELECT `when`,`i3`, `i2` FROM `App_info`
    WHERE `idBooth`=$idBooth AND `typeInfo`=20  ORDER BY `when` DESC  LIMIT 0 , 2 ; ";


APP_fesLogDebbug("Repdc_collection TRACE02 sql:$sql","logDebug20170220");


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "Error Database report collection, error code: 0002 $sql";
return;
}
$cashInReport = "";//20170612repCash
$playsInReport = "";//20170627repCash
if($APP_BdD->FetchRs()){//és el que acabem d'insertar
   $dataFinal = $APP_BdD->GetFieldDateTime(1);
   //20220127 la versio v3.1.5 té un bug de conversio de decimals i guarda valors enormes 65535 és el màxim permés. Si el valor es incorrecte no mostrem dades
   if($APP_BdD->GetField(2)==65535){
      $cashInReport = ""; 
   }else{
      $cashInReport = $APP_BdD->GetField(2);//20170612repCash 
   }
   
   $playsInReport = $APP_BdD->GetField(3);//20170627repCash

}
$dataInicial = null;
if($APP_BdD->FetchRs()){//és l'enterior
   $dataInicial = $APP_BdD->GetFieldDateTime(1);
}
$APP_BdD->CloseRs();


//20150709dateOwner INICI
if($dataMin != null){
    if($dataInicial == null){
        $dataInicial = clone $dataMin;
    }
    else{
        if($dataInicial < $dataMin) $dataInicial = clone $dataMin;
    }
}
//20150709dateOwner FINAL

if($dataInicial == null){//serà el primer registre d'App_info
    $sql = "SELECT `when` FROM `App_info`
        WHERE `idBooth`=$idBooth ORDER BY `when`  LIMIT 0 , 1 ;";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
        echo "Error Database report collection, error code: 0003 $sql";
        return;
    }
    if($APP_BdD->FetchRs()){//
           $dataInicial = $APP_BdD->GetFieldDateTime(1);
    }
    else{
        echo "Error Database report collection, error code: 0004 $sql";
        return;
    }
    
}
//$dataFinal= new DateTime($APP_inTimeSerial);
$htmlBegin.= "<p><strong>";
$htmlBegin.= "<span style='font-size:larger;background-color:#F00;color:#FFF'>COLLECTION Report</span><br/>";
$htmlBegin.= "From ".APP_myDateAndTime($dataInicial)." to ".APP_myDateAndTime($dataFinal)."</strong></p>";

//20150626            $toSubject = "COLLECTION Report from your PB "; 
            
            $toSubject = "COLLECTION Report $APP_reportNumber from your PB "; 
            
            
//echo "#TRACE htmlBegin: $htmlBegin";   //a eliminar

$htmlEnd = "
<p>Report Made: ".APP_myDate(new DateTime("now"))."<br/>
If you need any support, please contact the DC Team at <a href='mailto:main@dc-image.com'>main@dc-image.com</a><br/>
Digital Centre &copy; All Rights reserved</p>
";


$htmlReceived = "<p style='margin-bottom:0px;'>You received this email because your email   address is registered as the owner of this PhotoBooth.</p>
<ul style='margin-top:0px;'>
  <li>If you don&rsquo;t want to receive any more Reports please go to <a href=\"/index.php\">MyPhotoCode.com</a>, and switch OFF the Report emails from this Photobooth.</li>
  <li>If you are not the owner, please contact <a href=\"mailto:main@dc-image.com\">main@dc-image.com</a> to UNSUBSCRIBE for this service</li>
</ul>
";
       
//20150626     $rand_string = "";
//20150626    $sql = "SELECT  booths.`rand_string` FROM `booths` WHERE booths.id = $APP_idDongle";
//20150626    $esOK = $APP_BdD->OpenRs($sql);
//20150626    if($esOK){
//20150626        if($APP_BdD->FetchRs()){
//20150626            $rand_string  = $APP_BdD->GetField(1); 
//20150626        }
//20150626        $APP_BdD->CloseRs();
//20150626    }
//20160626: afegir reportNumber, ccsn, rand-string des de common
    
    $mail_cont = $htmlBegin;
    $mail_cont.= "
<table border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH MODEL:</td>
    <td>$type</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH S/N:</td>
    <td>$sn</td>
  </tr>";
    
    $mail_cont.= "
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH ID:</td>
    <td>$APP_idBooth</td>
  </tr>";
    
    
    $mail_cont.= "
  <tr>
    <td style='padding-right:10px;'>STRING:</td>
    <td>$APP_rand_string</td>
  </tr>";
    
    if(strlen($APP_reportNumber) > 0){
    $mail_cont.= "
  <tr>
    <td style='padding-right:10px;'>REPORT #:</td>
    <td>#$APP_reportNumber</td>
  </tr>";
    }
    //20170612repCash INICI
//    if($idBooth==7732){
    APP_fesLogDebbug("Repdc_collection cashInReport:$cashInReport","logDebug20170612");
    if(strlen($cashInReport) > 0){
    $mail_cont.= "
  <tr>
    <td>CASH IN REPORT:</td>
    <td>$cashInReport</td>
  </tr>";
        
    }
//    }
    //20170612repCash INICI
    
    //20170627repCash INICI
    APP_fesLogDebbug("Repdc_collection playsInReport:$playsInReport","logDebug20170612");
    if(strlen($playsInReport) > 0){
    $mail_cont.= "
  <tr>
    <td>PLAYS IN REPORT:</td>
    <td>$playsInReport</td>
  </tr>";
        
    }
    
    //20170627repCash FINAL
    
    
    if(strlen($ccsn) > 0){
    $mail_cont.= "
  <tr>
    <td style='padding-right:10px;'>CC READER S/N:</td>
    <td>$ccsn</td>
  </tr>";
    }
    
    $mail_cont.= "
  <tr>
    <td style='padding-right:10px;'>PAPER STOCK:</td>
    <td>$APP_common_stock</td>
  </tr>";
    
    $mail_cont.= "
  <tr>
    <td style='padding-right:10px;'>OWNER:</td>
    <td>$owner</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH NAME:</td>
    <td>$name</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH LOCATION:</td>
    <td >$location</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>SOFTWARE VERSION:</td>
    <td >$version</td>
  </tr>
</table>        ";
    
    
//20140601    $mail_cont.= $htmlReceived;//20140529
    
    $mail_cont.= "<hr />";//20140601
    
//20140521  INICI  
//freeplays
    $hihaFreeplays = false;
    $sql="SELECT DISTINCT App_info.currency, App_currencies.name, App_currencies.position, App_currencies.symbol FROM App_info INNER JOIN App_currencies ON App_info.currency = App_currencies.code ";
    $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($dataFinal,true).") AND idBooth={$idBooth} ";

APP_fesLogDebbug("Repdc_collection TRACE03 sql:$sql","logDebug20170220");

    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "Error Database report, error code: 0002a $sql";
    return;
    }
 //20170220!!!   while($APP_BdD->FetchRs()){
    if($APP_BdD->FetchRs()){//20170220!!!
        $hihaFreeplays = true;
    }
    $APP_BdD->CloseRs();
        
//20140521  FINAL  
    
    
//20170612rep2 INICI
    //per a versions Britta v1.1 seguirem calculant input cash a partir del cobrat en cash
    //Britta v1.1
    $cashFromMoney = true;
    if(strlen($version) > 6){
        $onlyBritta = substr($version,0,6);
        APP_fesLogDebbug("Repdc_collection cashFromMoney, check version $version, $onlyBritta","logDebug20170612");
        if(strcmp($onlyBritta, "Britta") == 0){//és Britta
            if(strcmp($version, "Britta v1.1") == 0){//és Britta v1.1
                APP_fesLogDebbug("Repdc_collection cashFromMoney, $version == Britta v1.1","logDebug20170612");
            }
            else{
                APP_fesLogDebbug("Repdc_collection cashFromMoney, $version <> Britta v1.1","logDebug20170612");
                $cashFromMoney = false;
            }

        }
    }
    
    
//20170612rep2 FINAL
    
    
    $sql="SELECT DISTINCT App_info.currency, App_currencies.name, App_currencies.position, App_currencies.symbol FROM App_info INNER JOIN App_currencies ON App_info.currency = App_currencies.code ";
    $sql.=" WHERE `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($dataFinal,true).") AND idBooth={$idBooth} ";
    $sql.=" ORDER BY App_info.currency; ";

APP_fesLogDebbug("Repdc_collection TRACE04 sql:$sql","logDebug20170220");

    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "Error Database report, error code: 0002b $sql";
    return;
    }
    $nCurrencies = 0;
    while($APP_BdD->FetchRs()){
        $array_currencies[$nCurrencies]         = $APP_BdD->GetField(1); 
        $array_currenciesName[$nCurrencies]     = utf8_encode($APP_BdD->GetField(2)); 
        $array_currenciesPosition[$nCurrencies] = $APP_BdD->GetField(3); 
        $array_currenciesSymbol[$nCurrencies]   = $APP_BdD->GetField(4); 
        $nCurrencies++;
    }
    $APP_BdD->CloseRs();
    
    if(!$nCurrencies && !$hihaFreeplays){
        $mail_cont.= "<p>NO INFORMATION AVAILABLE</p>";
        
        $mail_cont.= "<p  style='margin-bottom:0px;'>No Information available means that the server didn´t receive any data from this PhotoBooth. That could be for the following reasons:</p>
<ol style='margin-top:0px;'>
  <li>The PhotoBooth is not connected to Internet</li>
  <li>The PhotoBooth is connected to Internet, but the signal was down during that period of time.</li>
  <li>The PhotoBooth had no activity or was off</li>
</ol>

";
        
    }

APP_fesLogDebbug("Repdc_collection TRACE b abans for $nCurrencies","logDebug20170220");

   
    for($c = 0; $c<$nCurrencies; $c++){
        $mail_cont.= "<p><strong>Amounts in $array_currenciesName[$c]</strong></p>";
//20150715lifeTime        $mail_cont.= Repdc_moneyByPayment($APP_BdD,$idBooth,$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);//20150626

APP_fesLogDebbug("Repdc_collection TRACE c Repdc_moneyByPaymentNew for {$array_currenciesName[$c]}","logDebug20170220");

//20170612rep2         $mail_cont.= Repdc_moneyByPaymentNew($APP_BdD,$idBooth,$dataInicial,$dataFinal,$dataMin,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);//20150715lifeTime
        $mail_cont.= Repdc_moneyByPaymentNew($APP_BdD,$idBooth,$dataInicial,$dataFinal,$dataMin,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c],$cashFromMoney);//20170612rep2 
 
APP_fesLogDebbug("Repdc_collection TRACE c Repdc_daily for {$array_currenciesName[$c]}","logDebug20170220");

       $mail_cont.= Repdc_daily($APP_BdD,$idBooth,$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);//20160709daily
 
APP_fesLogDebbug("Repdc_collection TRACE c Repdc_moneyByProductNew for {$array_currenciesName[$c]}","logDebug20170220");

        //$mail_cont.= Repdc_moneyByProductNew($APP_BdD,$idBooth,$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);
        /*
         * 2022-04-25
         * Afegim versió desglosada de Repdc_moneyByProductNew. substituïm Repdc_moneyByProductNew per Repdc_moneyByProductNew2 a petició Josep
         */
        $mail_cont.= Repdc_moneyByProductNew2($APP_BdD,$idBooth,$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);
        $mail_cont.= "<p></p>";
    }
    
    
//20140529 INICI  

APP_fesLogDebbug("Repdc_collection TRACE b abans if $hihaFreeplays","logDebug20170220");

    
    if($hihaFreeplays){
        $mail_cont.= "<p><strong>Free plays</strong></p>";
        $mail_cont.= Repdc_freeplaysByProduct($APP_BdD,$idBooth,$dataInicial,$dataFinal);
        $mail_cont.= "<p></p>";
    }
//20140529 FINAL  

APP_fesLogDebbug("Repdc_collection TRACE b abans Repdc_activityNew","logDebug20170220");

    
//20150626 INICI  
    $mail_cont.= "<p><strong>Activity</strong></p>";
 
APP_fesLogDebbug("Repdc_collection TRACE c Repdc_activityNew","logDebug20170220");

    $mail_cont.= Repdc_activityNew($APP_BdD,$idBooth,$dataInicial,$dataFinal); //
    $mail_cont.= "<p></p>";
//20150626 FINAL  
    
    $mail_cont.= "<hr />";//20140601
    $mail_cont.= $htmlReceived;//20140601
    
    
    $mail_cont.= $htmlEnd;
    
    //cal enviar-lo per eMail
    
    $mail_email = $mail; 
    $mail_nom = $owner; 
    
    $mail_replayto = "main@dc-image.com";
 //   $mail_email = "victor.carretero@treemes.com";//Periode de proves!!!!!!!
 //   $mail_email = "jtarres@dc-image.com";//Periode de proves!!!!!!!
//20150613    $mail_email = "main@dc-image.com";//Periode de proves!!!!!!!
//20150613    $mail_nom.=  "-TEST";//Periode de proves!!!!!!!

    $mail_remitent = "main@dc-image.com";
    $mail_nomremitent = "DC Report Platform";
    $mail_copia = "main@dc-image.com";//20150625
//    $mail_copia = "victor.carretero@treemes.com";//20150625

    $mail_copia1 = "";
    $mail_copianom1 = "";
    $mail_copia2 = "";
    $mail_copianom2 = "";
//20150626    $mail_subject = $toSubject.$name; 
    $mail_subject = $toSubject.$name.". Location name: $location"; //20150626


APP_fesLogDebbug("Repdc_collection TRACE abans APP_mail","logDebug20170220");

    
   include('../common/APP_mail.php');



APP_fesLogDebbug("Repdc_collection TRACE FINAL","logDebug20170220");



?>
