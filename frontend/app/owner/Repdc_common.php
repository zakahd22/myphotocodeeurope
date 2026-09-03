<?php

function Repdc_moneyByPayment($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition)
{
//201506  NOTA: com que i3 no és fiable a Exp, llegirem el money total. El cash serà money - Card - Net
//201506  NOTA: trec    width='100%' de la cella Cash 
    
$ret_html = "";

//201506  $sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net FROM `App_info` ";
$sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net,  SUM(`money`) AS myMoney FROM `App_info` ";//201506  

//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ;";

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";//20141215

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByPayment, error code: 0001 ";//$sql";

}


while($APP_BdD->FetchRs()){
    $Cash = $APP_BdD->GetField(1);
    $Card = $APP_BdD->GetField(2);
    $Net  = $APP_BdD->GetField(3);
    $Money  = $APP_BdD->GetField(4);//201506
//    $Cash = $Money - $Card - $Net;//201506
    $Money = $Cash + $Card + $Net;
    
    //
    
  $ret_html.= "<table width='400' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='2'>Income by payment mode</td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>Payment mode</strong></td>
    <td align='right' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>money</strong></td>
  </tr>
  <tr>
    <td >Cash</td>
    <td align='right'>".Repdc_writeAmount($Cash,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td>Card</td>
    <td align='right'>".Repdc_writeAmount($Card,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td>Net</td>
    <td align='right'>".Repdc_writeAmount($Net,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($Cash+$Card+$Net,$currSymbol,$currPosition)."</strong></td>
  </tr>
</table>
<br/>
";
  
//  <tr>
//    <td colspan='2'><p>Note: income information by payment mode is collected from the Expression 2.0 version.</p>
//    <p></p>
//    </td>
//  </tr>
  //Afegir, si els valors no coicideixen potser algú ha pagat de més (p. ex. $5 quan val $4)
  
}
$APP_BdD->CloseRs();
return $ret_html;
}

function Repdc_moneyByPaymentNew2($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition)
{
//201506  NOTA: com que i3 no és fiable a Exp, llegirem el money total. El cash serà money - Card - Net
//201506  NOTA: trec    width='100%' de la cella Cash 

$ret_html = "";

//201506  $sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net FROM `App_info` ";
$sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net,  SUM(`money`) AS myMoney FROM `App_info` ";//201506  

//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ;";

$sql.=" WHERE currency='$currency' AND `typeInfo` IN (10,60) AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";//20141215

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByPayment, error code: 0001 ";//$sql";

}


while($APP_BdD->FetchRs()){
    $Cash = $APP_BdD->GetField(1);
    $Card = $APP_BdD->GetField(2);
    $Net  = $APP_BdD->GetField(3);
    $Money  = $APP_BdD->GetField(4);//201506
//    $Cash = $Money - $Card - $Net;//201506
    $Money = $Cash + $Card + $Net;
    
    //
    
  $ret_html.= "<table width='400' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='2'>Income by payment mode</td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>Payment mode</strong></td>
    <td align='right' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>money</strong></td>
  </tr>
  <tr>
    <td >Cash</td>
    <td align='right'>".Repdc_writeAmount($Cash,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td>Card</td>
    <td align='right'>".Repdc_writeAmount($Card,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td>Net</td>
    <td align='right'>".Repdc_writeAmount($Net,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($Cash+$Card+$Net,$currSymbol,$currPosition)."</strong></td>
  </tr>
</table>
<br/>
";
  
//  <tr>
//    <td colspan='2'><p>Note: income information by payment mode is collected from the Expression 2.0 version.</p>
//    <p></p>
//    </td>
//  </tr>
  //Afegir, si els valors no coicideixen potser algú ha pagat de més (p. ex. $5 quan val $4)
  
}
$APP_BdD->CloseRs();
return $ret_html;
}



//20150715lifeTime INICI
//20170612rep2 function Repdc_moneyByPaymentNew($APP_BdD,$idBooth,$dataInicial,$data,$datMin,$currency,$currSymbol,$currPosition)
function Repdc_moneyByPaymentNew($APP_BdD,$idBooth,$dataInicial,$data,$datMin,$currency,$currSymbol,$currPosition,$cashFromMoney = true)//20170612rep2 
{
//201506  NOTA: com que i3 no és fiable a Exp, llegirem el money total. El cash serà money - Card - Net
//201506  NOTA: trec    width='100%' de la cella Cash 
    
    
//20170223 INICI
//20170223$sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net,  SUM(`money`) AS myMoney FROM `App_info` ";  
//20170223$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($datMin)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";
    //$datMin pot ser null i està petant
     if($dataMin != null){
         $sqlDatMin = " `when` >=".$APP_BdD->myDateTimeSerial($datMin)." AND ";
         
     }
     else{
         $sqlDatMin = "";
     }
$sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net,  SUM(`money`) AS myMoney FROM `App_info` ";  
//20170620cash $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND ($sqlDatMin `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";
$sql.=" WHERE currency='$currency' AND `typeInfo` IN(10,60) AND ($sqlDatMin `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";//20170620cash 
//20170223 FINAL

APP_fesLogDebbug("Repdc_moneyByPaymentNew sql:$sql","logDebug20170220");

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByPayment, error code: 00011 ";//$sql";
}
if($APP_BdD->FetchRs()){
    $CashL = $APP_BdD->GetField(1);
    $CardL = $APP_BdD->GetField(2);
    $NetL  = $APP_BdD->GetField(3);
    $MoneyL  = $APP_BdD->GetField(4);
//20170612rep2     $CashL = $MoneyL - $CardL - $NetL;
    if($cashFromMoney) $CashL = $MoneyL - $CardL - $NetL;//20170612rep2  
}
$APP_BdD->CloseRs();
    
    
    
    
$ret_html = "";

//201506  $sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net FROM `App_info` ";
$sql ="SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net,  SUM(`money`) AS myMoney FROM `App_info` ";//201506  

//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ;";

//20170620cash $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";//20141215
$sql.=" WHERE currency='$currency' AND `typeInfo` IN(10,60) AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ;";//20170620cash 

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByPayment, error code: 0001 ";//$sql";

}

//while($APP_BdD->FetchRs()){
if($APP_BdD->FetchRs()){
    $Cash = $APP_BdD->GetField(1);
    $Card = $APP_BdD->GetField(2);
    $Net  = $APP_BdD->GetField(3);
    $Money  = $APP_BdD->GetField(4);//201506
//20170612rep2    $Cash = $Money - $Card - $Net;//201506
    if($cashFromMoney) $Cash = $Money - $Card - $Net;//20170612rep2  
    
    //
    
  $ret_html.= "<table width='450' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='3'><strong>Income by payment mode</strong></td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;'>&nbsp;</td>
    <td  colspan='2' align='center' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>money</strong></td>
  </tr>
  <tr>
    <td style='border-bottom:#000 solid 1px;'><strong>Payment mode</strong></td>
    <td align='right' style='border-bottom:#000 solid 1px;'><strong>current</strong></td>
    <td align='right' style='border-bottom:#000 solid 1px; color:#00F;'><strong>lifetime</strong></td>
  </tr>
  <tr>
    <td >Cash</td>
    <td align='right'>".Repdc_writeAmount($Cash,$currSymbol,$currPosition)."</td>
    <td align='right' style='color:#00F;'>".Repdc_writeAmount($CashL,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td>Card</td>
    <td align='right'>".Repdc_writeAmount($Card,$currSymbol,$currPosition)."</td>
    <td align='right' style='color:#00F;'>".Repdc_writeAmount($CardL,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td>Net</td>
    <td align='right'>".Repdc_writeAmount($Net,$currSymbol,$currPosition)."</td>
    <td align='right' style='color:#00F;'>".Repdc_writeAmount($NetL,$currSymbol,$currPosition)."</td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($Cash+$Card+$Net,$currSymbol,$currPosition)."</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px; color:#00F;' align='right'><strong>".Repdc_writeAmount($CashL+$CardL+$NetL,$currSymbol,$currPosition)."</strong></td>
  </tr>
</table>
<br/>
";
  
//  <tr>
//    <td colspan='2'><p>Note: income information by payment mode is collected from the Expression 2.0 version.</p>
//    <p></p>
//    </td>
//  </tr>
  //Afegir, si els valors no coicideixen potser algú ha pagat de més (p. ex. $5 quan val $4)
  
}
$APP_BdD->CloseRs();
return $ret_html;
}
//20150715lifeTime FINAL




function Repdc_moneyByProduct($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition)
{
$ret_html = "";

    
$sql ="SELECT  App_products.descr, myInfo.myPlays, myInfo.myMoney  FROM App_products LEFT JOIN  ";
$sql.=" (SELECT i1, SUM(`money`) AS myMoney, COUNT(*) AS myPlays FROM `App_info`  ";
//20140521  $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";
//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";//20140521

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215


$sql.=" GROUP BY i1) AS myInfo ON  App_products.id = myInfo.i1 ";
$sql.=" WHERE (App_products.id BETWEEN 1 AND 999 OR App_products.id=1031 )   ORDER BY App_products.id";

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0002 $sql";//$sql";

}

$ret_html.= "<table width='400' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='4'><strong>Money by product in play mode</strong></td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>Product</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>plays</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'>&nbsp;</td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>money</strong></td>
  </tr>
";


$totalPlays = 0;
$totalMoney = 0;
while($APP_BdD->FetchRs()){
    $Prod = $APP_BdD->GetField(1);
    $plays = $APP_BdD->GetField(2);
    if(!$plays) $plays = 0;
    $money = $APP_BdD->GetField(3);

    $ret_html.= "  <tr>
    <td>$Prod</td>
    <td align='right'>$plays</td>
    <td>&nbsp;</td>
    <td align='right'>".Repdc_writeAmount($money,$currSymbol,$currPosition)."</td>
    </tr>
    ";

    $totalPlays+= $plays;
    $totalMoney+= $money;

}
$APP_BdD->CloseRs();

//20140521 INICI
//other products
$sql = "SELECT count(*) AS myPlays, SUM(`money`) AS myMoney FROM `App_info` ";
//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0003 $sql";//$sql";

}
if($APP_BdD->FetchRs()){
    $plays = $APP_BdD->GetField(1);
    if(!$plays) $plays = 0;
    $money = $APP_BdD->GetField(2);
    
    if($plays > 0){

    $ret_html.= "  <tr>
    <td>Other products</td>
    <td align='right'>$plays</td>
    <td>&nbsp;</td>
    <td align='right'>".Repdc_writeAmount($money,$currSymbol,$currPosition)."</td>
    </tr>
    ";

    $totalPlays+= $plays;
    $totalMoney+= $money;
    
    }

}
$APP_BdD->CloseRs();

//20140521 FINAL


    $ret_html.= "
  <tr>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlays</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'>&nbsp;</td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalMoney,$currSymbol,$currPosition)."</strong></td>
  </tr>
</table>
        ";




return $ret_html;
}




function Repdc_moneyByProductNew($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition)
{
$ret_html = "";

    
$sql ="SELECT  App_products.descr, myInfo.myPlays, myInfo.myMoney, myInfo.myMoney2, myInfo.myPrints  FROM App_products LEFT JOIN  ";
$sql.=" (SELECT i1, SUM(`money`) AS myMoney, SUM(`money2`) AS myMoney2, COUNT(*) AS myPlays, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS myPrints FROM `App_info`  ";
//20140521  $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";
//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";//20140521

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215


$sql.=" GROUP BY i1) AS myInfo ON  App_products.id = myInfo.i1 ";
$sql.=" WHERE (App_products.id BETWEEN 1 AND 999 OR App_products.id=1031 )   ORDER BY App_products.id";

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!


APP_fesLogDebbug("Repdc_moneyByProductNew sql:$sql","logDebug20170220");


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0002 $sql";//$sql";

}

$ret_html.= "<table width='500' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='6'><strong>Money by product in play mode</strong></td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>Product</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>plays</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>prints</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>initial</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>upsell</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>total</strong></td>
  </tr>
";


$totalPlays = 0;
$totalPrints = 0;
$totalMoney = 0;
$totalMoney2 = 0;
while($APP_BdD->FetchRs()){
    $Prod = $APP_BdD->GetField(1);
    $plays = $APP_BdD->GetField(2);
    if(!$plays) $plays = 0;
    $money = $APP_BdD->GetField(3);
    $money2 = $APP_BdD->GetField(4);
    $initial = $money - $money2;
    $prints = $APP_BdD->GetField(5);
    if(!$prints) $prints = 0;
    
    if($money2 == 0){
        $strUpsell= "-";
    }
    else{
        $strUpsell= Repdc_writeAmount($money2,$currSymbol,$currPosition);
    }

    $ret_html.= "  <tr>
    <td>$Prod</td>
    <td align='right'>$plays</td>
    <td align='right'>$prints</td>
    <td align='right'>".Repdc_writeAmount($initial,$currSymbol,$currPosition)."</td>
    <td align='right'>$strUpsell</td>
    <td align='right'>".Repdc_writeAmount($money,$currSymbol,$currPosition)."</td>
    </tr>
    ";

    $totalPlays+= $plays;
    $totalPrints+= $prints;
    $totalMoney+= $money;
    $totalMoney2+= $money2;

}
$APP_BdD->CloseRs();

//20140521 INICI
//other products
$sql = "SELECT count(*) AS myPlays, SUM(`money`) AS myMoney, SUM(`money2`) AS myMoney2, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS myPrints FROM `App_info` ";
//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0003 $sql";//$sql";

}
if($APP_BdD->FetchRs()){
    $plays = $APP_BdD->GetField(1);
    if(!$plays) $plays = 0;
    $money = $APP_BdD->GetField(2);
    $money2 = $APP_BdD->GetField(3);
    $initial = $money - $money2;
    $prints = $APP_BdD->GetField(4);
    if(!$prints) $prints = 0;
    
    if($money2 == 0){
        $strUpsell= "-";
    }
    else{
        $strUpsell= Repdc_writeAmount($money2,$currSymbol,$currPosition);
    }
    if($plays > 0){

    $ret_html.= "  <tr>
    <td>Other products</td>
    <td align='right'>$plays</td>
    <td align='right'>$prints</td>
    <td align='right'>".Repdc_writeAmount($initial,$currSymbol,$currPosition)."</td>
    <td align='right'>$strUpsell</td>
    <td align='right'>".Repdc_writeAmount($money,$currSymbol,$currPosition)."</td>
    </tr>
    ";

    $totalPlays+= $plays;
    $totalPrints+= $prints;
    $totalMoney+= $money;
    $totalMoney2+= $money2;
    }

}
$APP_BdD->CloseRs();

//20140521 FINAL

    $initial = $totalMoney - $totalMoney2;
    
    if($totalMoney2 == 0){
        $strUpsell= "-";
    }
    else{
        $strUpsell= Repdc_writeAmount($totalMoney2,$currSymbol,$currPosition);
    }


    $ret_html.= "
  <tr>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlays</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPrints</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($initial,$currSymbol,$currPosition)."</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$strUpsell</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalMoney,$currSymbol,$currPosition)."</strong></td>
  </tr>
</table>
        ";




return $ret_html;
}

function Repdc_moneyByProductNew2($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition)
{
    
$freePlaysArray =   Repdc_freeplaysByProductGetArray($APP_BdD,$idBooth,$dataInicial,$data);
$totalFreePlays = $freePlaysArray['totalPlays'];
$ret_html = "";

    
$sql ="SELECT  App_products.descr, myInfo.myPlays, myInfo.myMoney, myInfo.myMoney2, myInfo.myPrints, myInfo.upsellAndExtraPrints, myInfo.initialPrints, App_products.id  FROM App_products "
        . "LEFT JOIN  ";
$sql.=" (SELECT i1, SUM(`money`) AS myMoney, SUM(`money2`) AS myMoney2, COUNT(*) AS myPlays, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS myPrints "
        . ",COALESCE(SUM(App_info.`in4`), 0) AS upsellAndExtraPrints, COALESCE(SUM(App_info.`in8`),0) initialPrints "
        . " FROM `App_info`  ";

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215


$sql.=" GROUP BY i1) AS myInfo ON  App_products.id = myInfo.i1 ";
$sql.=" WHERE (App_products.id BETWEEN 1 AND 999 OR App_products.id=1031 )   ORDER BY App_products.id";

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!


APP_fesLogDebbug("Repdc_moneyByProductNew sql:$sql","logDebug20170220");


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0002 $sql";//$sql";

}

$ret_html.= "<br><table width='750' border='0' cellpadding='4' cellspacing='0' >
  <tr>
    <td colspan='10'><strong>Money by product in play mode</strong></td>
  </tr>
  <tr>
    <td colspan='2' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'></td>
    <td style='border-top:#000 solid 2px;'>&nbsp;</td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' colspan='4' align='center'><strong>Prints</strong></td>
    <td style='border-top:#000 solid 2px;'>&nbsp;</td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' colspan='3' align='center'><strong>Money</strong></td>
  </tr>
  <tr>
    <td style='border-left:#000 solid 1px;border-bottom:#000 solid 1px;'><strong>Product</strong></td>
    <td style='border-right:#000 solid 1px;border-bottom:#000 solid 1px;' align='right'><strong>plays</strong></td>
    <td></td>
    <td style='border-left:#000 solid 1px;border-bottom:#000 solid 1px;' align='right'><strong>initial</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>upsell</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>free play</strong></td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;border-bottom:#000 solid 1px;' align='right'><strong>total</strong></td>
    <td></td>
    <td style='border-left:#000 solid 1px;border-bottom:#000 solid 1px;' align='right'><strong>initial</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>upsell</strong></td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;border-bottom:#000 solid 1px;' align='right'><strong>total</strong></td>
  </tr>
";


$totalPlays = 0;
$totalPrints = 0;
$totalMoney = 0;
$totalMoney2 = 0;
while($APP_BdD->FetchRs()){
    $productId = $APP_BdD->GetField(8);
    $Prod = $APP_BdD->GetField(1);
    $plays = $APP_BdD->GetField(2);
    if(!$plays) $plays = 0;
    $plays += $freePlaysArray['FreePlaysArray'][$productId];
    $money = $APP_BdD->GetField(3);
    $money2 = $APP_BdD->GetField(4);
    $initial = $money - $money2;
    $prints = $APP_BdD->GetField(5); //total
    if(!$prints) $prints = 0;
    $prints += $freePlaysArray['FreePrintsArray'][$productId];
    $upsellAndExtraPrints = $APP_BdD->GetField(6); //upsell And Extra Copies Prints
    if(!$upsellAndExtraPrints) $upsellAndExtraPrints = 0;
    $initialPrints = $APP_BdD->GetField(7); //initial Prints
    if(!$initialPrints) $initialPrints = 0;
    
    
    
    if($money2 == 0){
        $strUpsell= "-";
    }
    else{
        $strUpsell= Repdc_writeAmount($money2,$currSymbol,$currPosition);
    }

    $ret_html.= "  <tr>
    <td style='border-left:#000 solid 1px;'>$Prod</td>
    <td style='border-right:#000 solid 1px;' align='right'>$plays</td>
    <td></td>
    <td style='border-left:#000 solid 1px;' align='right'>$initialPrints</td>
    <td align='right'>$upsellAndExtraPrints</td>
    <td align='right'>".$freePlaysArray['FreePrintsArray'][$productId]."</td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;' align='right'>$prints</td>
    <td></td>
    <td style='border-left:#000 solid 1px;' align='right'>".Repdc_writeAmount($initial,$currSymbol,$currPosition)."</td>
    <td align='right'>$strUpsell</td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;' align='right'>".Repdc_writeAmount($money,$currSymbol,$currPosition)."</td>
    </tr>
    ";

    $totalPlays+= $plays;
    $totalPrints+= $prints;
    $totalUpExtraPrints+= $upsellAndExtraPrints;
    $totalInitialPrints+= $initialPrints;   
    $totalMoney+= $money;
    $totalMoney2+= $money2;

}
$APP_BdD->CloseRs();

//20140521 INICI
//other products
$sql = "SELECT count(*) AS myPlays, SUM(`money`) AS myMoney, SUM(`money2`) AS myMoney2, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS myPrints"
        . ",COALESCE(SUM(App_info.`in4`), 0) AS upsellAndExtraPrints, COALESCE(SUM(App_info.`in8`),0) initialPrints "
        . " FROM `App_info` ";
//20141215 $sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0003 $sql";//$sql";

}
if($APP_BdD->FetchRs()){
    $plays = $APP_BdD->GetField(1);
    if(!$plays) $plays = 0;
    $money = $APP_BdD->GetField(2);
    $money2 = $APP_BdD->GetField(3);
    $initial = $money - $money2;
    $prints = $APP_BdD->GetField(4);
    if(!$prints) $prints = 0;
    //TODO: afegeix a la consulta 
    // 
    $plays += $freePlaysArray['FreePlaysArray']['999'];


    $prints += $freePlaysArray['FreePrintsArray']['999'];
    $upsellAndExtraPrints = $APP_BdD->GetField(5); //upsell And Extra Copies Prints
    if(!$upsellAndExtraPrints) $upsellAndExtraPrints = 0;
    $initialPrints = $APP_BdD->GetField(6); //initial Prints
    if(!$initialPrints) $initialPrints = 0;
    

    if(!$prints) $prints = 0;
    
    if($money2 == 0){
        $strUpsell= "-";
    }
    else{
        $strUpsell= Repdc_writeAmount($money2,$currSymbol,$currPosition);
    }
    if($plays > 0){

    $ret_html.= "  <tr>
    <td style='border-left:#000 solid 1px;'>Other products</td>
    <td style='border-right:#000 solid 1px;' align='right'>$plays</td>
    <td></td>
    <td style='border-left:#000 solid 1px;' align='right'>$initialPrints</td>
    <td align='right'>$upsellAndExtraPrints</td>
    <td align='right'>".$freePlaysArray['FreePrintsArray']['999']."</td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;' align='right'>$prints</td>
    <td></td>
    <td style='border-left:#000 solid 1px;' align='right'>".Repdc_writeAmount($initial,$currSymbol,$currPosition)."</td>
    <td align='right'>$strUpsell</td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;' align='right'>".Repdc_writeAmount($money,$currSymbol,$currPosition)."</td>
    </tr>
    "; 
 


    $totalPlays+= $plays;
    $totalPrints+= $prints;    
    $totalUpExtraPrints+= $upsellAndExtraPrints;
    $totalInitialPrints+= $initialPrints;
    $totalMoney+= $money;
    $totalMoney2+= $money2;
    }

}
$APP_BdD->CloseRs();

//20140521 FINAL

    $initial = $totalMoney - $totalMoney2;
    
    if($totalMoney2 == 0){
        $strUpsell= "-";
    }
    else{
        $strUpsell= Repdc_writeAmount($totalMoney2,$currSymbol,$currPosition);
    }


    $ret_html.= "
  <tr>
    <td style='border-left:#000 solid 1px;border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-right:#000 solid 1px;border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlays</strong></td>
    <td></td>        
    <td style='border-left:#000 solid 1px;border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalInitialPrints</strong></td> 
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalUpExtraPrints</strong></td>       
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalFreePlays</strong></td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPrints</strong></td>
    <td></td>
    <td style='border-left:#000 solid 1px;border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($initial,$currSymbol,$currPosition)."</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$strUpsell</strong></td>
    <td style='border-left:#000 solid 1px;border-right:#000 solid 1px;border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalMoney,$currSymbol,$currPosition)."</strong></td>
  </tr>
</table>
        ";




return $ret_html;
}

function Repdc_freeplaysByProductGetArray($APP_BdD,$idBooth,$dataInicial,$data)
{


    
$sql ="SELECT  App_products.descr, myInfo.myPlays, App_products.id, myInfo.myPrints FROM App_products LEFT JOIN  ";
$sql.=" (SELECT i1, COUNT(*) AS myPlays, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS myPrints FROM `App_info`  ";
//20141215 $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215

$sql.=" GROUP BY i1) AS myInfo ON  App_products.id = myInfo.i1 ";
$sql.=" WHERE (App_products.id BETWEEN 1 AND 999 OR App_products.id=1031 )   ORDER BY App_products.id";



APP_fesLogDebbug("Repdc_freeplaysByProductGetArray sql:$sql","logDebug20170220");



$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_freeplaysByProductGetArray, error code: 0002 $sql";//$sql";

}


$FreePlaysArray = Array();
$totalPlays = 0;
//$totalMoney = 0;
while($APP_BdD->FetchRs()){
    $Prod = $APP_BdD->GetField(1);
    $plays = $APP_BdD->GetField(2);
    $id = $APP_BdD->GetField(3);
    $prints = $APP_BdD->GetField(4);
    if(!$plays) $plays = "0";
    $FreePlaysArray[$id]=$plays;
    if(!$prints) $prints = "0";
    $FreePrintsArray[$id]=$prints;
    

    $totalPlays+= $plays;


}
$APP_BdD->CloseRs();


//other products
$sql = "SELECT count(*) AS myPlays, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS myPrints FROM `App_info` ";
//20141215 $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE `typeInfo`=10 AND money IS NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0003 $sql";//$sql";

}
if($APP_BdD->FetchRs()){
    $plays = $APP_BdD->GetField(1);
    $prints = $APP_BdD->GetField(2);
    $FreePlaysArray['999']=$plays;    
    $FreePrintsArray['999']=$prints;    
    
    if($plays > 0){       

        $totalPlays+= $plays;

    
    }

}
$APP_BdD->CloseRs();



   $result['FreePlaysArray'] = $FreePlaysArray;
   $result['FreePrintsArray'] = $FreePrintsArray;
   $result['totalPlays'] = $totalPlays;    



return $result;
}



function Repdc_freeplaysByProduct($APP_BdD,$idBooth,$dataInicial,$data)
{
$ret_html = "";

    
$sql ="SELECT  App_products.descr, myInfo.myPlays  FROM App_products LEFT JOIN  ";
$sql.=" (SELECT i1, COUNT(*) AS myPlays FROM `App_info`  ";
//20141215 $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE `typeInfo`=10 AND money IS NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215

$sql.=" GROUP BY i1) AS myInfo ON  App_products.id = myInfo.i1 ";
$sql.=" WHERE (App_products.id BETWEEN 1 AND 999 OR App_products.id=1031 )   ORDER BY App_products.id";

//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!

APP_fesLogDebbug("Repdc_freeplaysByProduct sql:$sql","logDebug20170220");



$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0002 $sql";//$sql";

}

$ret_html.= "<table width='300' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='2'><strong>Free plays by product</strong></td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>Product</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>plays</strong></td>
  </tr>
";


$totalPlays = 0;
//$totalMoney = 0;
while($APP_BdD->FetchRs()){
    $Prod = $APP_BdD->GetField(1);
    $plays = $APP_BdD->GetField(2);
    if(!$plays) $plays = "0";

    $ret_html.= "  <tr>
    <td>$Prod</td>
    <td align='right'>$plays</td>
    </tr>
    ";

    $totalPlays+= $plays;
//    $totalMoney+= $money;

}
$APP_BdD->CloseRs();


//other products
$sql = "SELECT count(*) AS myPlays FROM `App_info` ";
//20141215 $sql.=" WHERE `typeInfo`=10 AND money IS NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE `typeInfo`=10 AND money IS NULL AND `i1` >999 AND i1 <> 1031 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_moneyByProduct, error code: 0003 $sql";//$sql";

}
if($APP_BdD->FetchRs()){
    $plays = $APP_BdD->GetField(1);
    
    if($plays > 0){

    $ret_html.= "  <tr>
    <td>Other products</td>
    <td align='right'>$plays</td>
    </tr>
    ";

    $totalPlays+= $plays;
//    $totalMoney+= $money;
    
    }

}
$APP_BdD->CloseRs();



    $ret_html.= "
  <tr>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
    <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlays</strong></td>
  </tr>
</table>
        ";




return $ret_html;
}



function Repdc_writeAmount($import,$simbol,$posicio)
{

    
    if(strlen($import) == 0){
       // return ":";
        $strimport = "0";
    }
    else{
   //     $strimport = sprintf("%.0f",intval($import));
        $strimport = intval($import);
    }
    switch($posicio){
        default:
        case 0:
            return "$strimport $simbol";
            break;
        case 1:
            return $simbol.$strimport;
            break;
        
    }
    

}



//tipus d'informació a APP_info
// `typeInfo`=20 : Report
// `typeInfo`=10 : Play
// `typeInfo`=40 AND `i1`=1 : Printer Error
// `typeInfo`=40 AND `i1`=2 : Peper error
// `typeInfo`=40 AND `i1`=3 : Board Error
// `typeInfo`=40 AND `i1`=4 : Cam Error
//

function Repdc_activity($APP_BdD,$idBooth,$dataInicial,$data)
{
$ret_html = "";

$sql ="SELECT `when`,`typeInfo`,i1,App_products.descr FROM App_info LEFT JOIN App_products ON App_info.i1 = App_products.id ";
//20141215 $sql.=" WHERE (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215
//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!

    
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_activity, error code: 0002 $sql";//$sql";

}

$ret_html.= "<table width='300' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='3'><strong>Activity report</strong></td>
  </tr>
  <tr>
    <td colspan='3' style='border-top:#000 solid 2px;'><strong>&nbsp;</strong></td>
  </tr>
";

$lastData = 0;
while($APP_BdD->FetchRs()){
    $quan = $APP_BdD->GetFieldDateTime(1);
    $data = intval($quan->format("Ymd"));  //
    if($data > $lastData){
        $ret_html.= "
            <tr>
              <td colspan='3'>".$quan->format("m-d-Y")."</td>
            </tr>
          ";
         $lastData = $data;
    }
    $temps = $quan->format("h:i A");
    $tipus = $APP_BdD->GetField(2);
    $i1 = $APP_BdD->GetField(3);
    $descr = $APP_BdD->GetField(4);
    $text = "&nbsp;";
    switch($tipus){
        case 10:
            $text = "Play: $descr";
            break;
        case 20:
            $text = "Cash Collection";
            break;
        case 40:
            switch($i1){
            case 1:
                $text = "Printer Error";
                break;
            case 2:
                $text = "Paper Error";
                break;
            case 3:
                $text = "I/O Board Error";
                break;
            case 4:
                $text = "Camera Error";
                break;
            }
            break;
        
//20170612 INICI 
        case 50:
            $text = "Session Start";
            break;
        case 60:
            $text = "Session End";
            break;
//20170612 FINAL     
            
    }
    $ret_html.= "
        <tr>
          <td>&nbsp;</td><td>$temps</td><td>$text</td>
        </tr>
      ";
}
$APP_BdD->CloseRs();

    $ret_html.= "
  <tr>
    <td colspan='3' style='border-bottom:#000 solid 2px;'>&nbsp;</td>
  </tr>
</table>
        ";




return $ret_html;
}

//20150709daily INICI
//funció per a la dataInicial mínima dels reports (data de venda) CLD_date_tOwner
function Repdc_getDateOwner($APP_BdD,$idBooth) 
{
    $sql = "SELECT `CLD_date_tOwner` FROM `App_booths` WHERE `idBooth`=$idBooth; ";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
//        echo "Error Database report collection, error code: 0002 $sql";
        return null;
    }
    if($APP_BdD->FetchRs()){//
       $dataM = $APP_BdD->GetFieldDateTime(1);
    }
    else{
        $dataM = null;
    }

    return $dataM;
}

//funció per a la dataInicial del report daily
function Repdc_dailyGetInit($APP_BdD,$idBooth,$data) 
{
    $sql = "SELECT `when`, `i1` FROM `App_info`
        WHERE `idBooth`=$idBooth AND `typeInfo`=20  ORDER BY `when` DESC  LIMIT 1 , 1 ; ";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
//        echo "Error Database report collection, error code: 0002 $sql";
        return null;
    }
    if($APP_BdD->FetchRs()){//
       $dataI = $APP_BdD->GetFieldDateTime(1);
    }
    else{
        $dataI = clone $data;
    }

    $APP_BdD->CloseRs();
}
/*******
 * 2022-05-30 NOVA VERSIÓ
 * AFEGIM type60 per sumar els cash i el card correctament tal i com ho fem a audits
 */

function Repdc_daily($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition) 
{
$sql ="SELECT `when`,`typeInfo`,COALESCE(money, 0) as money ,COALESCE(i4, 0) as i4,COALESCE(i5, 0) as i5, COALESCE(stock, 0) as stock ,COALESCE(in4, 0) as in4 , COALESCE(in8, 0) as in8 FROM App_info";
$sql.=" WHERE currency='$currency' AND ((`typeInfo`=10 AND money IS NOT NULL) OR (`typeInfo`=60 AND (i4>0 OR i5>0))) AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";
$sql.=" ORDER BY `when` ";


APP_fesLogDebbug("Repdc_daily sql:$sql","logDebug20170220");


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Daily report, error code: 0002 ";//$sql";

}
    $ret_html = "";
$ret_html = "<table width='730' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='9'><strong>Daily report</strong></td>
  </tr>
  <tr>
    <td colspan='3' style='border-top:#000 solid 2px;'>&nbsp;</td>
    <td colspan='4' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='center'><strong>money</strong></td>
    <td colspan='2' style='border-top:#000 solid 2px;'>&nbsp;</td>
  </tr>
  <tr>
    <td style='border-bottom:#000 solid 1px;' width='220'><strong>Date</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>plays</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>prints</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>cash</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>card</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>net</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>total</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right' width='110'><strong>&nbsp;stock:</strong> initial</td>
    <td style='border-bottom:#000 solid 1px;' align='right'>final</td>
  </tr>
  ";

$nLinies = 0;
$lastQuan = $dataInicial;
$lastData = intval($dataInicial->format("Ymd"));
//$lastStrQuan = ":";
$nPlays = 0;
$nPrints = 0;
$dailyMoney=0;
$dailyCash = 0;
$dailyCard = 0;
$dailyNet = 0;
$contaFirst = 0;
$lastStock = ":";
$firstStock = ":";

$totalPlays = 0;
$totalPrints = 0;
$totalMoney = 0;
$totalCash = 0;
$totalCard = 0;
$totalNet = 0;
while($APP_BdD->FetchRs()){
//    $card = $cash = $net = $money = 0;
    $camp = 1;
    $quan = $APP_BdD->GetFieldDateTime($camp); $camp++;
    $tipus = $APP_BdD->GetField($camp); $camp++;
    $money = intval($APP_BdD->GetField($camp)); $camp++;
    $card = intval($APP_BdD->GetField($camp)); $camp++;
    $net = intval($APP_BdD->GetField($camp)); $camp++;
    $stock = $APP_BdD->GetField($camp); $camp++;
    $prints = $APP_BdD->GetField($camp); $camp++;
    $upsells = $APP_BdD->GetField($camp); $camp++; 
    if($tipus==10){
       $prints = $prints + $upsells; 
    }
    
    $dataQuan = intval($quan->format("Ymd"));  //
    $cash = $money - $card - $net;
    if(!isset($nextStock[$nLinies-1])){
        $firstStock = $AuxFirstStock+$prints; //Calculem primer stock del report en base a l'stock de la primera partida + els prints de la mateixa = stock inicial
    }else{
        $firstStock = $nextStock[$nLinies-1];
    }        
    
   
    if($dataQuan > $lastData){//canvi de dia
        $contaFirst = 0;
        
              
          
        if($nLinies){
            $strQuan = $lastQuan->format("m-d-Y");
         }
         else{
            $strQuan = $lastQuan->format("m-d-Y") . " from ". $lastQuan->format("h:i A");
           
            
         }         
         if($nPlays){
             if(!$lastStock) $lastStock=0;
        $ret_html.= "
             <tr>
               <td>$strQuan</td>
               <td align='right'>$nPlays</td>
               <td align='right'>$nPrints</td>
            <td align='right'>".Repdc_writeAmount($dailyCash,$currSymbol,$currPosition)."</td>
            <td align='right'>".Repdc_writeAmount($dailyCard,$currSymbol,$currPosition)."</td>
            <td align='right'>".Repdc_writeAmount($dailyNet,$currSymbol,$currPosition)."</td>
               <td align='right'>".Repdc_writeAmount($dailyMoney,$currSymbol,$currPosition)."</td>
               <td align='right'>$firstStock</td>
               <td align='right'>$lastStock</td>
             </tr>
           ";
         }
         else{
        $ret_html.= "
             <tr>
               <td>$strQuan</td>
               <td align='right'>:</td>
               <td align='right'>:</td>
            <td align='right'>:</td>
            <td align='right'>:</td>
            <td align='right'>:</td>
               <td align='right'>:</td>
               <td align='right'>:</td>
               <td align='right'>:</td>
             </tr>
           ";
             
         }
        $nPlays = 0;
        $nPrints = 0;
        $dailyMoney=0;
        $dailyCash = 0;
        $dailyCard = 0;
        $dailyNet = 0;

        $lastQuan = $quan;
        $lastData = $dataQuan;
        if($tipus==10){
            $nLinies++;
        }
        
    }
    if($tipus==10){
        $nPlays++;
        $nPrints+=$prints;
        $totalPlays++;
        $totalPrints+=$prints;
        if(!$contaFirst ){
        $AuxFirstStock = $stock;
        $contaFirst++;
        }

        $nextStock[$nLinies] = $stock;
        $lastStock = $stock;
    }    
    $dailyMoney+=$money;
    $dailyCash+=$cash;
    $dailyCard+=$card;
    $dailyNet+=$net;
    
    $totalMoney+=$money;
    
    $totalCash+=$cash;
    $totalCard+=$card;
    $totalNet+=$net;
    
    

}
$APP_BdD->CloseRs();

//darrer dia
if($nPlays){
    //$strQuan = $data->format("m-d-Y") . " to ". $data->format("h:i A");
    //només afegim la hora final si la última partida és el mateix dia. si no el dia serà complet i no cal
    //I no podem assegurar que la partida hagi estat el dia del collection, $data->format("m-d-Y") per tant estava malament
    if($strQuan == $data->format("m-d-Y")){
        $strQuan = $data->format("m-d-Y") . " to ". $data->format("h:i A");
    }else{
        $strQuan = $quan->format("m-d-Y");
    }
    $firstStock = $nextStock[$nLinies-1];
    
    $ret_html.= "
         <tr>
           <td>$strQuan</td>
           <td align='right'>$nPlays</td>
           <td align='right'>$nPrints</td>
           <td align='right'>".Repdc_writeAmount($dailyCash,$currSymbol,$currPosition)."</td>
           <td align='right'>".Repdc_writeAmount($dailyCard,$currSymbol,$currPosition)."</td>
           <td align='right'>".Repdc_writeAmount($dailyNet,$currSymbol,$currPosition)."</td>
           <td align='right'>".Repdc_writeAmount($dailyMoney,$currSymbol,$currPosition)."</td>
           <td align='right'>$firstStock</td>
           <td align='right'>$lastStock</td>
         </tr>
       ";
        $nLinies++;
}

    $ret_html.= "
        <tr>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlays</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPrints</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalCash,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalCard,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalNet,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalMoney,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'>&nbsp;</td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'>&nbsp;</td>
        </tr>
      </table>
        <br/>
        ";


return $ret_html;
}

//20220530 daily FINAL




//ANTIGA VERSIO, no tenia en compte els type60 i no quadra el cash i card
//
//function Repdc_daily($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition) 
//{
//$sql ="SELECT `when`,`typeInfo`,money,i4,i5, stock, in4, in8 FROM App_info";
//$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND money IS NOT NULL AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";
//$sql.=" ORDER BY `when` ";
//
//
//APP_fesLogDebbug("Repdc_daily sql:$sql","logDebug20170220");
//
//
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
////caldria controlar l'error
//return "Error Database Daily report, error code: 0002 ";//$sql";
//
//}
//    $ret_html = "";
//$ret_html = "<table width='730' border='0' cellpadding='1' cellspacing='0' >
//  <tr>
//    <td colspan='9'><strong>Daily report</strong></td>
//  </tr>
//  <tr>
//    <td colspan='3' style='border-top:#000 solid 2px;'>&nbsp;</td>
//    <td colspan='4' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='center'><strong>money</strong></td>
//    <td colspan='2' style='border-top:#000 solid 2px;'>&nbsp;</td>
//  </tr>
//  <tr>
//    <td style='border-bottom:#000 solid 1px;' width='220'><strong>Date</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right'><strong>plays</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right'><strong>prints</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right'><strong>cash</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right'><strong>card</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right'><strong>net</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right'><strong>total</strong></td>
//    <td style='border-bottom:#000 solid 1px;' align='right' width='110'><strong>&nbsp;stock:</strong> initial</td>
//    <td style='border-bottom:#000 solid 1px;' align='right'>final</td>
//  </tr>
//  ";
//
//$nLinies = 0;
//$lastQuan = $dataInicial;
//$lastData = intval($dataInicial->format("Ymd"));
////$lastStrQuan = ":";
//$nPlays = 0;
//$nPrints = 0;
//$dailyMoney=0;
//$dailyCash = 0;
//$dailyCard = 0;
//$dailyNet = 0;
//$contaFirst = 0;
//$lastStock = ":";
//$firstStock = ":";
//
//$totalPlays = 0;
//$totalPrints = 0;
//$totalMoney = 0;
//$totalCash = 0;
//$totalCard = 0;
//$totalNet = 0;
//while($APP_BdD->FetchRs()){
////    $card = $cash = $net = $money = 0;
//    $camp = 1;
//    $quan = $APP_BdD->GetFieldDateTime($camp); $camp++;
//    $tipus = $APP_BdD->GetField($camp); $camp++;
//    $money = intval($APP_BdD->GetField($camp)); $camp++;
//    $card = intval($APP_BdD->GetField($camp)); $camp++;
//    $net = intval($APP_BdD->GetField($camp)); $camp++;
//    $stock = $APP_BdD->GetField($camp); $camp++;
//    $prints = $APP_BdD->GetField($camp); $camp++;
//    $upsells = $APP_BdD->GetField($camp); $camp++;    
//    $prints = $prints + $upsells;
//    $dataQuan = intval($quan->format("Ymd"));  //
//    $cash = $money - $card - $net;
//    if(!isset($nextStock[$nLinies-1])){
//        $firstStock = $AuxFirstStock+$prints; //Calculem primer stock del report en base a l'stock de la primera partida + els prints de la mateixa = stock inicial
//    }else{
//        $firstStock = $nextStock[$nLinies-1];
//    }        
//    
//   
//    if($dataQuan > $lastData){//canvi de dia
//        $contaFirst = 0;
//        
//              
//          
//        if($nLinies){
//            $strQuan = $lastQuan->format("m-d-Y");
//         }
//         else{
//            $strQuan = $lastQuan->format("m-d-Y") . " from ". $lastQuan->format("h:i A");
//           
//            
//         }         
//         if($nPlays){
//             if(!$lastStock) $lastStock=0;
//        $ret_html.= "
//             <tr>
//               <td>$strQuan</td>
//               <td align='right'>$nPlays</td>
//               <td align='right'>$nPrints</td>
//            <td align='right'>".Repdc_writeAmount($dailyCash,$currSymbol,$currPosition)."</td>
//            <td align='right'>".Repdc_writeAmount($dailyCard,$currSymbol,$currPosition)."</td>
//            <td align='right'>".Repdc_writeAmount($dailyNet,$currSymbol,$currPosition)."</td>
//               <td align='right'>".Repdc_writeAmount($dailyMoney,$currSymbol,$currPosition)."</td>
//               <td align='right'>$firstStock</td>
//               <td align='right'>$lastStock</td>
//             </tr>
//           ";
//         }
//         else{
//        $ret_html.= "
//             <tr>
//               <td>$strQuan</td>
//               <td align='right'>:</td>
//               <td align='right'>:</td>
//            <td align='right'>:</td>
//            <td align='right'>:</td>
//            <td align='right'>:</td>
//               <td align='right'>:</td>
//               <td align='right'>:</td>
//               <td align='right'>:</td>
//             </tr>
//           ";
//             
//         }
//        $nPlays = 0;
//        $nPrints = 0;
//        $dailyMoney=0;
//        $dailyCash = 0;
//        $dailyCard = 0;
//        $dailyNet = 0;
//
//        $lastQuan = $quan;
//        $lastData = $dataQuan;
//        
//        $nLinies++;
//        
//    }
//    $nPlays++;
//    $nPrints+=$prints;
//    $dailyMoney+=$money;
//    $dailyCash+=$cash;
//    $dailyCard+=$card;
//    $dailyNet+=$net;
//    $totalPlays++;
//    $totalMoney+=$money;
//    $totalPrints+=$prints;
//    $totalCash+=$cash;
//    $totalCard+=$card;
//    $totalNet+=$net;
//    if(!$contaFirst){
//        $AuxFirstStock = $stock;
//        $contaFirst++;
//    }
//    
//    $nextStock[$nLinies] = $stock;
//    $lastStock = $stock;
//    
//
//}
//$APP_BdD->CloseRs();
//
////darrer dia
//if($nPlays){
//    //$strQuan = $data->format("m-d-Y") . " to ". $data->format("h:i A");
//    //només afegim la hora final si la última partida és el mateix dia. si no el dia serà complet i no cal
//    //I no podem assegurar que la partida hagi estat el dia del collection, $data->format("m-d-Y") per tant estava malament
//    if($strQuan == $data->format("m-d-Y")){
//        $strQuan = $data->format("m-d-Y") . " to ". $data->format("h:i A");
//    }else{
//        $strQuan = $quan->format("m-d-Y");
//    }
//    $firstStock = $nextStock[$nLinies-1];
//    
//    $ret_html.= "
//         <tr>
//           <td>$strQuan</td>
//           <td align='right'>$nPlays</td>
//           <td align='right'>$nPrints</td>
//           <td align='right'>".Repdc_writeAmount($dailyCash,$currSymbol,$currPosition)."</td>
//           <td align='right'>".Repdc_writeAmount($dailyCard,$currSymbol,$currPosition)."</td>
//           <td align='right'>".Repdc_writeAmount($dailyNet,$currSymbol,$currPosition)."</td>
//           <td align='right'>".Repdc_writeAmount($dailyMoney,$currSymbol,$currPosition)."</td>
//           <td align='right'>$firstStock</td>
//           <td align='right'>$lastStock</td>
//         </tr>
//       ";
//        $nLinies++;
//}
//
//    $ret_html.= "
//        <tr>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlays</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPrints</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalCash,$currSymbol,$currPosition)."</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalCard,$currSymbol,$currPosition)."</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalNet,$currSymbol,$currPosition)."</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalMoney,$currSymbol,$currPosition)."</strong></td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'>&nbsp;</td>
//          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'>&nbsp;</td>
//        </tr>
//      </table>
//        <br/>
//        ";
//
//
//return $ret_html;
//}
//
////20150709daily FINAL

//20150730daily INICI

function Repdc_dailyNew($APP_BdD,$idBooth,$dataInicial,$data,$currency,$currSymbol,$currPosition) 
{
    //money can be NULL (freeplay)
$sql ="SELECT `when`,`typeInfo`,money,i4,i5, stock FROM App_info";
$sql.=" WHERE currency='$currency' AND `typeInfo`=10 AND (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";
$sql.=" ORDER BY `when` ";


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Daily report, error code: 0002 ";//$sql";

}
    $ret_html = "";
$ret_html = "<table width='500' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='9'><strong>Daily report</strong></td>
  </tr>
  <tr>
    <td style='border-top:#000 solid 2px;'>&nbsp;</td>
    <td colspan='2' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='center'><strong>plays</strong></td>
    <td style='border-top:#000 solid 2px;'>&nbsp;</td>
    <td colspan='4' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='center'><strong>money</strong></td>
    <td style='border-top:#000 solid 2px;'>&nbsp;</td>
  </tr>
  <tr>
    <td style='border-bottom:#000 solid 1px;'><strong>Date</strong></td>
    
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>paid</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>free</strong></td>
    <td style='border-bottom:#000 solid 1px;'>&nbsp;</td>
    
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>cash</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>card</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>net</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>total</strong></td>
    <td style='border-bottom:#000 solid 1px;' align='right'><strong>stock</strong></td>
  </tr>
  ";

$nLinies = 0;
$lastQuan = $dataInicial;
$lastData = intval($dataInicial->format("Ymd"));
//$lastStrQuan = ":";
$nPlays = 0;
$nPlaysPaid = 0;
$nPlaysFree = 0;
$dailyMoney=0;
$dailyCash = 0;
$dailyCard = 0;
$dailyNet = 0;
$lastStock = ":";

$totalPlays = 0;
$totalPlaysPaid = 0;
$totalPlaysFree = 0;
$totalMoney = 0;
$totalCash = 0;
$totalCard = 0;
$totalNet = 0;
while($APP_BdD->FetchRs()){
//    $card = $cash = $net = $money = 0;
    $camp = 1;
    $quan = $APP_BdD->GetFieldDateTime($camp); $camp++;
    $tipus = $APP_BdD->GetField($camp); $camp++;
    $money = intval($APP_BdD->GetField($camp)); $camp++;
    $card = intval($APP_BdD->GetField($camp)); $camp++;
    $net = intval($APP_BdD->GetField($camp)); $camp++;
    $stock = $APP_BdD->GetField($camp); $camp++;
    $dataQuan = intval($quan->format("Ymd"));  //
    $cash = $money - $card - $net;
    
    if($dataQuan > $lastData){//canvi de dia
        if($nLinies){
            $strQuan = $lastQuan->format("m-d-Y");
         }
         else{
            $strQuan = $lastQuan->format("m-d-Y") . " from ". $lastQuan->format("h:i A");
             
         }
         if($nPlays){
        $ret_html.= "
             <tr>
               <td>$strQuan</td>
               <td align='right'>$nPlaysPaid</td>
               <td align='right'>$nPlaysFree</td>
               <td>&nbsp;</td>
                   

            <td align='right'>".Repdc_writeAmount($dailyCash,$currSymbol,$currPosition)."</td>
            <td align='right'>".Repdc_writeAmount($dailyCard,$currSymbol,$currPosition)."</td>
            <td align='right'>".Repdc_writeAmount($dailyNet,$currSymbol,$currPosition)."</td>
               <td align='right'>".Repdc_writeAmount($dailyMoney,$currSymbol,$currPosition)."</td>
               <td align='right'>$lastStock</td>
             </tr>
           ";
         }
         else{
        $ret_html.= "
             <tr>
               <td>$strQuan</td>
               <td align='right'>:</td>
               <td align='right'>:</td>
               <td>&nbsp;</td>
            <td align='right'>:</td>
            <td align='right'>:</td>
            <td align='right'>:</td>
               <td align='right'>:</td>
               <td align='right'>:</td>
             </tr>
           ";
             
         }
        $nPlays = 0;
        $nPlaysPaid = 0;
        $nPlaysFree = 0;
        $dailyMoney=0;
        $dailyCash = 0;
        $dailyCard = 0;
        $dailyNet = 0;

        $lastQuan = $quan;
        $lastData = $dataQuan;
        $nLinies++;
    }
    $nPlays++;
    if($money){
        $nPlaysPaid++;
        $totalPlaysPaid++;
    }
    else{
        $nPlaysFree++;
        $totalPlaysFree++;
    }
    
    $dailyMoney+=$money;
    $dailyCash+=$cash;
    $dailyCard+=$card;
    $dailyNet+=$net;
    $totalPlays++;
    $totalMoney+=$money;
    $totalCash+=$cash;
    $totalCard+=$card;
    $totalNet+=$net;
    
    $lastStock = $stock;

}
$APP_BdD->CloseRs();

//darrer dia
if($nPlays){
    $strQuan = $data->format("m-d-Y") . " to ". $data->format("h:i A");
    $ret_html.= "
         <tr>
           <td>$strQuan</td>
            <td align='right'>$nPlaysPaid</td>
            <td align='right'>$nPlaysFree</td>
            <td>&nbsp;</td>
           <td align='right'>".Repdc_writeAmount($dailyCash,$currSymbol,$currPosition)."</td>
           <td align='right'>".Repdc_writeAmount($dailyCard,$currSymbol,$currPosition)."</td>
           <td align='right'>".Repdc_writeAmount($dailyNet,$currSymbol,$currPosition)."</td>
           <td align='right'>".Repdc_writeAmount($dailyMoney,$currSymbol,$currPosition)."</td>
           <td align='right'>$lastStock</td>
         </tr>
       ";
        $nLinies++;
}

    $ret_html.= "
        <tr>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'><strong>Total</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlaysPaid</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>$totalPlaysFree</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'>&nbsp;</td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalCash,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalCard,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalNet,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;' align='right'><strong>".Repdc_writeAmount($totalMoney,$currSymbol,$currPosition)."</strong></td>
          <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;'>&nbsp;</td>
        </tr>
      </table>
        <br/>
        ";


return $ret_html;
}

//20150730daily FINAL

function Repdc_activityNew($APP_BdD,$idBooth,$dataInicial,$data) //20150626
{
$ret_html = "";

//20150626  $sql ="SELECT `when`,`typeInfo`,i1,App_products.descr FROM App_info LEFT JOIN App_products ON App_info.i1 = App_products.id ";
$sql ="SELECT `when`,`typeInfo`,i1,App_products.descr, stock, money, in4 FROM App_info LEFT JOIN App_products ON App_info.i1 = App_products.id ";//20220426

//20141215 $sql.=" WHERE (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` < ".$APP_BdD->myDateTimeSerial($data,true).") AND idBooth=$idBooth ";

$sql.=" WHERE (`when` >=".$APP_BdD->myDateTimeSerial($dataInicial)." AND `when` <= ".$APP_BdD->myDateTimeSerial($data).") AND idBooth=$idBooth ";//20141215
//$ret_html.= "<br/>$sql<br/>";//a eliminar!!!!!!

$sql.=" ORDER BY `when` "; //20150710!!!!!!!!


APP_fesLogDebbug("Repdc_activityNew sql:$sql","logDebug20170220");


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
return "Error Database Repdc_activity, error code: 0002 $sql";//$sql";

}

$ret_html.= "<table width='400' border='0' cellpadding='1' cellspacing='0' >
  <tr>
    <td colspan='4'><strong>Activity report</strong></td>
  </tr>
  <tr>
    <td colspan='2' style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>date/time</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;'><strong>&nbsp;</strong></td>
    <td style='border-top:#000 solid 2px;border-bottom:#000 solid 1px;' align='right'><strong>stock</strong></td>
  </tr>
";

$lastData = 0;
while($APP_BdD->FetchRs()){
    $quan = $APP_BdD->GetFieldDateTime(1);
    $data = intval($quan->format("Ymd"));  //
    if($data > $lastData){
        $ret_html.= "
            <tr>
              <td colspan='4' style='border-bottom:#000 solid 1px;border-top:#000 solid 1px;'>".$quan->format("m-d-Y")."</td>
            </tr>
          ";
         $lastData = $data;
    }
    $temps = $quan->format("h:i A");
    $tipus = $APP_BdD->GetField(2);
    $i1 = $APP_BdD->GetField(3);
    $descr = $APP_BdD->GetField(4);
    $stock = $APP_BdD->GetField(5);//20150626
    $money = $APP_BdD->GetField(6);//20220426    
    $in4 = $APP_BdD->GetField(7);//20220426
    if(!$in4) $in4 = 0;
    $text = "&nbsp;";
    $freeText = "";
    $strStock = "&nbsp;";//20150626
    switch($tipus){
        case 10:
            if(!$money){
                $freeText = "Free ";
            }
            $text = $freeText."Play: $descr";
            $strStock = $stock;//20150626
            break;
        case 20:
            $text = "<strong>Cash Collection</strong>";
            break;
        case 40:
            switch($i1){
            case 1:
                $text = "Printer Error";
                break;
            case 2:
                $text = "Paper Error";
                break;
            case 3:
                $text = "I/O Board Error";
                break;
            case 4:
                $text = "Camera Error";
                break;
            }
            break;
        
//20170612 INICI        
        case 50:
            $text = "Session Start";
            break;
        case 60:
            $text = "Session End";
            break;
//20170612 FINAL     
            
        
    }
    if($in4>0){        
        $strStock = $strStock + $in4;       
        
    }
    $ret_html.= "
        <tr>
          <td>&nbsp;</td><td>$temps</td><td>$text</td>
          <td align='right'>$strStock</td>    
        </tr>
      ";
    if($in4>0){
        for($i=1;$i<=$in4;$i++){
            $strStock = $strStock - 1; 
            $ret_html.= "
                <tr>
                  <td>&nbsp;</td><td>$temps</td><td>Upsell / Extracopies</td>
                  <td align='right'>$strStock</td>    
                </tr>
              ";  
        }
      
    }
}
$APP_BdD->CloseRs();

    $ret_html.= "
  <tr>
    <td colspan='4' style='border-bottom:#000 solid 2px;'>&nbsp;</td>
  </tr>
</table>
        ";




return $ret_html;
}


?>