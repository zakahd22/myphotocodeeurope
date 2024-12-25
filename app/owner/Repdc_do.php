<?php
/*
 * $Rep_tipus  1: setmanal   2: mensual   3: anual
 * 201507 4: diari des del darrer collection
 * //sprintf("%02d.",$tmp);
 */

//20150709daily INICI
if($Rep_tipus == 4) $strFiltreOwner = " AND App_booths.owner IN (7,333) ";
else $strFiltreOwner = "";
//20150709daily FINAL


//$sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email FROM `App_booths` JOIN rentals ON App_booths.owner=rentals.id WHERE report='WEEKLY';";

$myBooths = "(SELECT `idBooth` FROM `App_boothConfigDef` WHERE `typeConfig`=1 AND `value`='YES')";
//20140529 INICI
//20140529   $sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email FROM `App_booths` JOIN rentals ON App_booths.owner=rentals.id WHERE idBooth IN $myBooths;";


//$sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email,booth_types.`name`,App_booths.`serialnumber` FROM (`App_booths` JOIN rentals ON App_booths.owner=rentals.id) ";
//$sql.=" JOIN booth_types ON App_booths.`type` = booth_types.`char` WHERE idBooth IN $myBooths $strFiltreOwner;";

$sql="SELECT `idBooth`,App_booths.`name`,location,version,rentals.name,rentals.APP_email,CLD_boothTypes.`name`,App_booths.`serialnumber` FROM (`App_booths` JOIN rentals ON App_booths.owner=rentals.id) ";
$sql.=" LEFT JOIN CLD_boothTypes ON App_booths.`CLD_idType` = CLD_boothTypes.`id` WHERE idBooth IN $myBooths $strFiltreOwner;";

//20140529 INICI
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "Error Database report, error code: 0001 $sql";
return;
}

//echo "TRACE $sql\r\n";


$nBooths = 0;
while($APP_BdD->FetchRs()){
    $array_idBooth[$nBooths] = $APP_BdD->GetField(1); 
    $array_name[$nBooths]    = $APP_BdD->GetField(2); 
    $array_location[$nBooths]= $APP_BdD->GetField(3); 
    $array_version[$nBooths] = $APP_BdD->GetField(4); 
    $array_owner[$nBooths]   = $APP_BdD->GetField(5); 
    $array_mail[$nBooths]    = $APP_BdD->GetField(6); 
    $array_type[$nBooths]    = $APP_BdD->GetField(7); 
    $array_sn[$nBooths]      = $APP_BdD->GetField(8); 
    
    
    $nBooths++;
}
$APP_BdD->CloseRs();
if(!$nBooths){
    return;
}
//20140601 INICI
//$htmlBegin = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
//<html xmlns='https://www.w3.org/1999/xhtml'>
//<head>
//<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
//<title>DC Report</title>
//</head>
//<body>
//<h1>DC PhotoBooth Report</h1>
//";

$htmlBegin = "<h1>DC PhotoBooth Report</h1>";

//20140601 FINAL

//$dataInicial = $APP_araMateix;
//$data = $APP_araMateix;
$dataInicial= clone $APP_araMateix;
$dataInicial->setTime(0,0,0);

//$data=clone $APP_araMateix;
switch($Rep_tipus){
    case 1://setmanal, $data correspon a un dilluns, ha de ser entre >=data-7 i <data
        $htmlBegin.= "<p><strong>";
        $htmlBegin.= "<span style='font-size:larger;background-color:#FF0;'>WEEKLY Report</span><br/>";
        $dataInicial->modify("-7 day");
        $diasetmana = $dataInicial->format("w");
        if($diasetmana != 1){
            if($diasetmana == 0) $toModify = "-6 day";
            else {$diasetmana--; $toModify = "-$diasetmana day";}
            $dataInicial->modify($toModify);
        }
        $dataFinal=clone $dataInicial;
        $dataFinal->modify("+6 day");
      //  $data->modify("-1 day");
        $htmlBegin.= "From ".APP_myDate($dataInicial)." to ".APP_myDate($dataFinal)."</strong></p>";
        
            $toSubject = "WEEKLY Report from your PB "; 

        break;
    case 2://mensual
        $htmlBegin.= "<p><strong>";
        $dataInicial->modify("-1 month");
        $htmlBegin.= "<span style='font-size:larger;background-color:#0F0;'>MONTHLY Report</span><br/>";
        $dataInicial->setDate($dataInicial->format("Y"),$dataInicial->format("n"),1);
        $dataFinal= new DateTime();
        $dataFinal->setDate($dataInicial->format("Y"),$dataInicial->format("n"), $dataInicial->format("t"));
        $htmlBegin.= $dataInicial->format("F") . " ".$dataInicial->format("Y")."</strong></p>";
        
            $toSubject = "MONTHLY Report from your PB "; 

        break;
    case 3://anual
        $htmlBegin.= "<p><strong>";
//20140429        $dataInicial->modify("-1 year");
        $dataInicial->modify("-1 month");//20140429 serà l'acumulat fins el mes anterior des de l'1 de gener de l'any corresponent
        $htmlBegin.= "<span style='font-size:larger;background-color:#0FF;'>YEAR Report</span><br/>";
        $dataFinal= new DateTime();
        $dataFinal->setDate($dataInicial->format("Y"),$dataInicial->format("n"), $dataInicial->format("t"));
        $dataInicial->setDate($dataInicial->format("Y"),1,1);
        $htmlBegin.= "From ".APP_myDate($dataInicial)." to ".APP_myDate($dataFinal)."</strong></p>";
        
            $toSubject = "YEAR Report from your PB "; 

        break;
    
//20150709daily INICI
    //ara està en test, i després serà només per a SunStar idOwner: 7,333 (veure $strFiltreOwner més amunt)
    case 4://daily
        $Rep_test = true;
        $htmlBegin.= "<p><strong>";
        $htmlBegin.= "<span style='font-size:larger;background-color:#8000FF;'>DAILY Report</span><br/>";
        $dataInicial->modify("-1 day");
        $dataFinal=clone $dataInicial;
        //cal buscar la data Inicial
        $htmlBegin.= "From ".APP_myDate($dataInicial)." to ".APP_myDate($dataFinal)."</strong></p>";
        
            $toSubject = "DAILY Report from your PB "; 

        break;
//20150709daily FINAL
        
    
    default:
        return "";
        break;
}

        $dataFinal->setTime(23,59,59);//20141215


//20150601 INICI
//$htmlEnd = "
//  <p>&nbsp;</p>
//<p>Report Made: ".APP_myDate($APP_araMateix)."<br/>
//If you need any support, please contact the DC Team at <a href='mailto:main@dc-image.com'>main@dc-image.com</a><br/>
//Digital Centre &copy; All Rights reserved</p>
//</body>
//</html>
//  
//";

//$htmlReceived = "<p>You received this email because your email   address is registered as the owner of this PhotoBooth.</p>
//<ul>
//  <li>If you don&rsquo;t want to receive any more Reports please go to <a href=\"https://www.myphotocode.com\">MyPhotoCode.com</a>, and switch OFF the Report emails from this Photobooth.</li>
//  <li>If you are not the owner, please contact <a href=\"mailto:main@dc-image.com\">main@dc-image.com</a> to UNSUBSCRIBE for this service</li>
//</ul>
//";

$htmlEnd = "
<p>Report Made: ".APP_myDate($APP_araMateix)."<br/>
If you need any support, please contact the DC Team at <a href='mailto:main@dc-image.com'>main@dc-image.com</a><br/>
Digital Centre &copy; All Rights reserved</p>
";


$htmlReceived = "<p style='margin-bottom:0px;'>You received this email because your email   address is registered as the owner of this PhotoBooth.</p>
<ul style='margin-top:0px;'>
  <li>If you don&rsquo;t want to receive any more Reports please go to <a href=\"/index.php\">MyPhotoCode.com</a>, and switch OFF the Report emails from this Photobooth.</li>
  <li>If you are not the owner, please contact <a href=\"mailto:main@dc-image.com\">main@dc-image.com</a> to UNSUBSCRIBE for this service</li>
</ul>
";

//20150601 FINAL



for($i = 0; $i<$nBooths; $i++){
    
    if(!$array_mail[$i]) continue;
        
    
//    $array_name[$i]    = $APP_BdD->GetField(2); 
//    $array_location[$i]= $APP_BdD->GetField(3); 
//    $array_version[$i] = $APP_BdD->GetField(4); 
    
//string
    $rand_string = "";
//    $sql = "SELECT  booths.`rand_string`
//    FROM `booths` INNER JOIN App_info ON booths.id = App_info.idDongle 
//    WHERE idBooth = {$array_idBooth[$i]}  ORDER BY `when` DESC LIMIT 0,1;";
    $sql = "SELECT  booths.`rand_string`
    FROM `booths` INNER JOIN App_boothDongle ON booths.id = App_boothDongle.idDongle 
    WHERE App_boothDongle.idBooth = {$array_idBooth[$i]}  ORDER BY `datetimeS` DESC LIMIT 0,1;";
    $esOK = $APP_BdD->OpenRs($sql);
    if($esOK){
        if($APP_BdD->FetchRs()){
            $rand_string  = $APP_BdD->GetField(1); 
        }
        $APP_BdD->CloseRs();
    }
  
    //Nota 20170105: afegim PBid
    
    $mail_cont = $htmlBegin;
    $mail_cont.= "
<table border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH MODEL:</td>
    <td>$array_type[$i]</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>S/N:</td>
    <td>$array_sn[$i]</td>
  </tr>
  
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH ID:</td>
    <td>$array_idBooth[$i]</td>
  </tr>


  <tr>
    <td style='padding-right:10px;'>STRING:</td>
    <td>$rand_string</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>OWNER:</td>
    <td>$array_owner[$i]</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH NAME:</td>
    <td>$array_name[$i]</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>PHOTOBOOTH LOCATION:</td>
    <td >$array_location[$i]</td>
  </tr>
  <tr>
    <td style='padding-right:10px;'>SOFTWARE VERSION:</td>
    <td >$array_version[$i]</td>
  </tr>
</table>        ";
    
    
//20140601    $mail_cont.= $htmlReceived;//20140529
    
    $mail_cont.= "<hr />";//20140601
    
//20140521  INICI  
//freeplays
    $hihaFreeplays = false;
    $sql="SELECT DISTINCT App_info.currency, App_currencies.name, App_currencies.position, App_currencies.symbol FROM App_info INNER JOIN App_currencies ON App_info.currency = App_currencies.code ";
//20141215    $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($dataFinal,true).") AND idBooth={$array_idBooth[$i]} ";
    $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($dataFinal).") AND idBooth={$array_idBooth[$i]} ";//20141215
    
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "Error Database report, error code: 0002a $sql";
    return;
    }
    while($APP_BdD->FetchRs()){
        $hihaFreeplays = true;
    }
    $APP_BdD->CloseRs();
        
//20140521  FINAL  
    
    
    $sql="SELECT DISTINCT App_info.currency, App_currencies.name, App_currencies.position, App_currencies.symbol FROM App_info INNER JOIN App_currencies ON App_info.currency = App_currencies.code ";
//20141215    $sql.=" WHERE `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($dataFinal,true).") AND idBooth={$array_idBooth[$i]} ";
    $sql.=" WHERE `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($dataFinal).") AND idBooth={$array_idBooth[$i]} ";//20141215

    $sql.=" ORDER BY App_info.currency; ";
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
   //?     $array_currenciesName[$nCurrencies]     = $APP_BdD->GetField(2); 
        $array_currenciesPosition[$nCurrencies] = $APP_BdD->GetField(3); 
   //?     $array_currenciesSymbol[$nCurrencies]   = utf8_encode($APP_BdD->GetField(4)); 
        $array_currenciesSymbol[$nCurrencies]   = $APP_BdD->GetField(4); 
        $nCurrencies++;
    }
    $APP_BdD->CloseRs();
    
//20140521    if(!$nCurrencies){
    if(!$nCurrencies && !$hihaFreeplays){//20140521
        $mail_cont.= "<p>NO INFORMATION AVAILABLE</p>";
 
//20140601 INICI
//        $mail_cont.= "<p style=>No Information available means that the server didn´t receive any data from this PhotoBooth. That could be for the following reasons:</p>
//<ol>
//  <li>The PhotoBooth is not connected to Internet</li>
//  <li>The PhotoBooth is connected to Internet, but the signal was down during that period of time.</li>
//  <li>The PhotoBooth had no activity or was off</li>
//</ol>
//
//";
        
        
        $mail_cont.= "<p  style='margin-bottom:0px;'>No Information available means that the server didn´t receive any data from this PhotoBooth. That could be for the following reasons:</p>
<ol style='margin-top:0px;'>
  <li>The PhotoBooth is not connected to Internet</li>
  <li>The PhotoBooth is connected to Internet, but the signal was down during that period of time.</li>
  <li>The PhotoBooth had no activity or was off</li>
</ol>

";
//20140601 FINAL
        
        
    }
    
//20140521  INICI  
    
//20140529    if($hihaFreeplays){
//20140529        $mail_cont.= "<p><strong>Free plays</strong></p>";
 //20140529       $mail_cont.= Repdc_freeplaysByProduct($APP_BdD,$array_idBooth[$i],$dataInicial,$dataFinal);
//20140529        $mail_cont.= "<p></p>";
//20140529    }
//20140521  FINAL  
    
    for($c = 0; $c<$nCurrencies; $c++){
        $mail_cont.= "<p><strong>Amounts in $array_currenciesName[$c]</strong></p>";
//201506  //de moment no!! els PB no envien la informació correctament        $mail_cont.= Repdc_moneyByPayment($APP_BdD,$array_idBooth[$i],$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);
        $mail_cont.= Repdc_moneyByPaymentNew2($APP_BdD,$array_idBooth[$i],$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);//201506 

        //Repdc_daily($APP_BdD,$idBooth,$data,$currency,$currSymbol,$currPosition)
        
        $mail_cont.= Repdc_moneyByProduct($APP_BdD,$array_idBooth[$i],$dataInicial,$dataFinal,$array_currencies[$c],$array_currenciesSymbol[$c],$array_currenciesPosition[$c]);
        $mail_cont.= "<p></p>";
    }
    
    
//20140529 INICI  
    
    if($hihaFreeplays){
        $mail_cont.= "<p><strong>Free plays</strong></p>";
        $mail_cont.= Repdc_freeplaysByProduct($APP_BdD,$array_idBooth[$i],$dataInicial,$dataFinal);
        $mail_cont.= "<p></p>";
    }
//20140529 FINAL  
    
//20140701 INICI  
    if($Rep_tipus == 1){
        $mail_cont.= "<p><strong>Activity</strong></p>";
        $mail_cont.= Repdc_activity($APP_BdD,$array_idBooth[$i],$dataInicial,$dataFinal); //
        $mail_cont.= "<p></p>";
    }
//20140701 FINAL  
    
    
    $mail_cont.= "<hr />";//20140601
    $mail_cont.= $htmlReceived;//20140601
    
    
    $mail_cont.= $htmlEnd;
    
    //cal enviar-lo per eMail
    
    $mail_email = $array_mail[$i]; 
    $mail_nom = $array_owner[$i]; 
    
    $mail_replayto = "main@dc-image.com";
 //   $mail_email = "victor.carretero@treemes.com";//Periode de proves!!!!!!!
//201506    $mail_email = "main@dc-image.com";//Periode de proves!!!!!!!
//201506    $mail_nom.=  "-TEST";//Periode de proves!!!!!!!
    
    
//20150709daily INICI
    if($Rep_test){
        $mail_email = "main@dc-image.com";
        $mail_nom.=  "-TEST";
        
    }
//20150709daily FINAL
    
    $mail_copia = "main@dc-image.com";//201506

    $mail_nomremitent = "DC Report Platform";

    $mail_copia1 = "";
    $mail_copianom1 = "";
    $mail_copia2 = "";
    $mail_copianom2 = "";
//Abans, al switch de tipus    $mail_subject = "APP Report from your PB $array_name[$i]"; 
    $mail_subject = $toSubject.$array_name[$i]; 
    include('../common/APP_mail.php');

}



?>
