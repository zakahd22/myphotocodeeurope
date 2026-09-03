<?php

require("../common/APP_BdD.php");

require("../common/APP_common.php");

//de moment un email
//$mail_email = "victor.carretero@treemes.com";
//$mail_nom = "Jo mateix";
//$mail_subject = "Missatge à";
//$mail_cont = "<p>Prova de mail</p>";
//include("common/APP_mail.php");

//busquem booths on s'hagi definit l'alerta offline i agafem hora inici i final de funcionament
//cal creuar-lo amb les alertes per a descartar els que ja han estat detectats



//$faestona = new DateTime("now"); $faestona->modify("-5 minute");//el marge de temps per a decidir si està parat és de 5 minuts

//el marge de temps per a decidir si està parat és de 15 minuts
//20140410  $faestona = new DateTime("now"); 
$APP_araMateix = new DateTime("now");//20140410 el volem per a reports setmanals, mensuals i anuals  
$faestona = clone $APP_araMateix;//20140410 




//aprofito per a fer log
//echo "\r\n";
//echo $faestona->format("Y-m-d H:i ");

//i el datetime per a la base
$APP_araTimeSerial = $APP_BdD->myDateTimeSerial($faestona);

echo "TRACE $APP_araTimeSerial\n";

$faestona->modify("-15 minute");

$APP_faestonaTimeSerial = $APP_BdD->myDateTimeSerial($faestona);

$daquiestona = new DateTime("now"); $daquiestona->modify("+15 minute");
//20131003 $horaS = $daquiestona->format("H");
//20131003 $minutS = $daquiestona->format("i");
//20131003 $horaE = $faestona->format("H");
//20131003 $minutE = $faestona->format("i");

$hmS = $daquiestona->format("Hi");//20131003 
$hmE = $faestona->format("Hi");//20131003 

$myBoothsOffline = " (SELECT DISTINCT `idBooth` FROM `App_boothAlert` 
    WHERE typeAlert = 1 AND estat < 2) ";

//20130524 INICI
//Canvis: una notificació per a cada PhotoBooth i incloure el nom

//20140615 Tz!!!! $sql = "SELECT owner, `idBooth`,`name` ";
//20150625location $sql = "SELECT owner, `idBooth`,`name`,`lastConnZone` ";//20140615 Tz!!!! 
//$sql = "SELECT owner, `idBooth`,`name`,`lastConnZone`, location ";//20150625location
//$sql.= " FROM `App_booths`  WHERE  idBooth NOT IN $myBoothsOffline AND alertOffline = 1";

$sql = "SELECT T1.owner, T1.idBooth, T1.name, T1.lastConnZone, T1.location, T2.name, T1.serialnumber, T1.version, T3.name, T5.rand_string 
       FROM App_booths T1 
       LEFT  JOIN CLD_boothTypes T2 ON T1.CLD_idType = T2.id 
       LEFT  JOIN rentals T3 ON T1.owner = T3.id 
       LEFT  JOIN App_boothDongle T4 ON T1.idBooth = T4.idBooth AND datetimeF IS NULL 
       LEFT  JOIN booths T5 ON T4.idDongle = T5.id 
       WHERE T1.idBooth NOT IN $myBoothsOffline AND alertOffline = 1 ";

//20130603midnight INICI
//20130603midnight  $sql.= " AND hS <= $horaS AND mS <= $minutS AND hE >= $horaE AND mE >= $minutE ";

//$sql.= " AND ((midnight=0 AND hS <= $horaS AND mS <= $minutS AND hE >= $horaE AND mE >= $minutE ) ";
//$sql.= " OR (midnight=1 AND (hS <= $horaS AND mS <= $minutS OR hE >= $horaE AND mE >= $minutE) ))";
//


//20131003 INICI
//ho farem fent un check a hmS i hmE que corresponen a hS, etc corretgides per a poder comprovar amb l'hora del servidor

$sql.= " AND (
( midnight=0 AND (hmS <= $hmS AND hmE >= $hmE))
OR
( midnight=1 AND ((hmS <= $hmS) OR (hmE >= $hmE)))
)
AND `lastConn` < $APP_faestonaTimeSerial
ORDER BY T4.datetimeS LIMIT 1 ";
//print $sql;exit;
// echo "TRACE $sql";
$nBooths = 0;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "Error Database error code: 0001 $sql";
    //return;
}
else{
    while($APP_BdD->FetchRs()){
        $array_owner[$nBooths] = $APP_BdD->GetField(1); 
        $array_idBooth[$nBooths] = $APP_BdD->GetField(2); 
        $array_name[$nBooths] = $APP_BdD->GetField(3); 
        $array_type[$nBooths] = $APP_BdD->GetField(6);
        $array_Snumber[$nBooths] = $APP_BdD->GetField(7);
        $array_version[$nBooths] = $APP_BdD->GetField(8);
        $array_nameowner[$nBooths] = $APP_BdD->GetField(9);
        if(is_null($APP_BdD->GetField(10))){
           $htmlRandString="";  
        }else{
            $htmlRandString="<tr>
                            <td style='padding-right:10px;'><b>STRING:</b></td>
                            <td>".$APP_BdD->GetField(10)."</td>
                          </tr>";  
        }
        
        
//20150627location        $array_location[$nBooths] = $APP_BdD->GetField(4); //20150625location
        
        $array_quanTimeSerial[$nBooths] = $APP_BdD->myDateTimeSerial($APP_BdD->GetFieldDateTime(4));  //20140615 Tz!!!! 
        
        $array_location[$nBooths] = $APP_BdD->GetField(5); //20150627location
        $nBooths++;
    }
    $APP_BdD->CloseRs();
}
if(!$nBooths){
    echo "End, no booths to check";
//    return;
    
}
else{
//20170220apns    include("../easyapns/src/php/APP_apns.php");
    //tindrem una llista de booths que hem de comprovar si App_sessions.last és >= $faestona
}
//20140508 INICI ho posaré més abaix
//    //actualitzarà l'estat del booth, també actualitzarà $APP_common_badge
//    include '../photobooth/common/APP_common_checkAlerts.php';
//    if($APP_common_error) return;
//20140508 FINAL ho posaré més abaix



for($i = 0; $i<$nBooths; $i++){
    
    
//20140508 INICI estava més amunt
$APP_idBooth = $array_idBooth[$i];//nou
//actualitzarà l'estat del booth, també actualitzarà $APP_common_badge
include '../photobooth/common/APP_common_checkAlerts.php';
if($APP_common_error) continue;//l´he canviat
//20140508 FINAL estava més amunt
    
    //insert de l'alerta
//20140615 Tz!!!!     $sql = "INSERT INTO App_boothAlert SET  idBooth = $array_idBooth[$i], `typeAlert` = 1, `estat` = 0, `when`=$APP_araTimeSerial";
//20140717nBooths!!!!    $sql = "INSERT INTO App_boothAlert SET  idBooth = $array_idBooth[$i], `typeAlert` = 1, `estat` = 0, `when`=$array_quanTimeSerial[$nBooths]";//20140615 Tz!!!! 
    $sql = "INSERT INTO App_boothAlert SET  idBooth = $array_idBooth[$i], `typeAlert` = 1, `estat` = 0, `when`=$array_quanTimeSerial[$i]";//20140717nBooths!!!! 
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        echo "Error - Database error code: 0003 $sql.";
//20140508        return;
        continue;//20140508
    }
    echo "TRACE, offline alert in PB {$array_idBooth[$i]}: {$array_name[$i]} to user {$array_owner[$i]}\r\n";

    $APP_common_badge++;
//20170220apns    $APNS_MessageAdded = APNS_addAlertOffline($array_owner[$i],$array_idBooth[$i],$array_name[$i],$APP_common_badge);
    
    
//20140421mails INICI        
            //cal enviar un email a l'owner
            $sql = "SELECT name,`App_email` FROM rentals WHERE id=$array_owner[$i]; ";
            $esOK = $APP_BdD->OpenRs($sql);
            if($esOK){
                 if($APP_BdD->FetchRs()){
                    $mail_nom =  $APP_BdD->GetField(1);
                    $mail_email =  $APP_BdD->GetField(2);
                    
                    if($mail_email){
    $mail_replayto = "main@dc-image.com";
//20141028alerts    $mail_email = "main@dc-image.com";//Periode de proves!!!!!!!
//20141028alerts                        $mail_nom.=  "-TEST";//Periode de proves!!!!!!!
    $mail_copia = "main@dc-image.com";//20141028alerts

                        $mail_remitent = "main@dc-image.com";//20150626
                        $mail_nomremitent = "DC Alerts Platform";

                        $mail_copia1 = "";
                        $mail_copianom1 = "";
                        $mail_copia2 = "";
                        $mail_copianom2 = "";
//20150625location                        $mail_subject = "Alert Detection Notification"; 
                        $mail_subject = "Alert Detection Notification. Location name: $array_location[$i]"; //20150625location
                        

                        $mail_cont = "<h1>$APP_araTimeSerial: An alert has been detected in one of your PhotoBooths</h1>";
                        $mail_cont.="<h2>Your Photobooth is OFFLINE</h2>";
                        $mail_cont.="
                            <table border='0' cellpadding='1' cellspacing='0' >
                               <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH NAME:</b></td>
                                <td>$array_name[$i]</td>
                              </tr>
                              <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH MODEL:</b></td>
                                <td>$array_type[$i]</td>
                              </tr>";
                        $mail_cont.="
                              <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH S/N:</b></td>
                                <td>$array_Snumber[$i]</td>
                              </tr>";
                        $mail_cont.= $htmlRandString;
                        $mail_cont.="
                              <tr>
                                <td style='padding-right:10px;'><b>PHOTOBOOTH LOCATION:</b></td>
                                <td>$array_location[$i]</td>
                              </tr>
                              <tr>
                                <td style='padding-right:10px;'>Id:</td>
                                <td>$array_idBooth[$i]</td>
                              </tr>";
                         $mail_cont.="
                              <tr>
                                <td style='padding-right:10px;'>Owner:</td>
                                <td>$array_nameowner[$i]</td>
                              </tr>
                              <tr>
                                <td style='padding-right:10px;'>Version:</td>
                                <td>$array_version[$i]</td>
                              </tr>
                            </table>";
                        $mail_cont.= "<br/><tr>You received this email because your email address is registered as the owner of a DC PhotoBooth</tr></br>";
                        $mail_cont.= "<br/><tr>Digital Centre</tr>";
                        include('../common/APP_mail.php');
                    }
                 }
                 $APP_BdD->CloseRs();
            }
//20140421mails FINAL        
    

}


//20131003 FINAL

//20140409 INICI
//
    
//20140508 INICI a eliminar!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//$Rep_diaSetm = intval($APP_araMateix->format("w"));
//
//    echo "TRACE, Rep_diaSetm: $Rep_diaSetm (".$APP_araMateix->format("G:i").")\r\n";
//
//
//if($Rep_diaSetm == 4){ //dijous
//    //possible informe setmanal
//    $Rep_hora = intval($APP_araMateix->format("G"));
//    if($Rep_hora == 17){
//        $Rep_minut = intval($APP_araMateix->format("i"));
//        if($Rep_minut > 10 && $Rep_minut < 20){ //el cron s'executa als quarts d'hora
//            //cal fer reports setmanals
//            $Rep_tipus = 1;
//            include_once ('Repdc_common.php');
//            include('Repdc_do.php');
//
//        }
//    }
//    
//    
//}
    
//20140508 FINAL a eliminar!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//
//
//reports periòdics
$Rep_diaSetm = intval($APP_araMateix->format("w"));
if($Rep_diaSetm == 2){ //dimarts
    //possible informe setmanal
    $Rep_hora = intval($APP_araMateix->format("G"));
    if($Rep_hora == 8){
        $Rep_minut = intval($APP_araMateix->format("i"));
        if($Rep_minut > 10 && $Rep_minut < 20){ //el cron s'execute als quarts d'hora, i el volem a les 8:15
            //cal fer reports setmanals
            $Rep_tipus = 1;
            include_once ('Repdc_common.php');
            include('Repdc_do.php');

        }
    }
    
    
}
$Rep_diaMes = intval($APP_araMateix->format("j"));
if($Rep_diaMes == 2){//el dia 2
    //possible informe mensual
    $Rep_hora = intval($APP_araMateix->format("G"));
    if($Rep_hora == 8){
        $Rep_minut = intval($APP_araMateix->format("i"));
        if($Rep_minut > 25 && $Rep_minut < 35){ //el cron s'execute als quarts d'hora, i el volem a les 8:30
            //cal fer reports mensuals
            $Rep_tipus = 2;
            include_once ('Repdc_common.php');
            include('Repdc_do.php');

        }
    }
    
}

//20140605 INICI
//el YEARLY s'enviarà cada mes anb l'accumulat des de l'1 de gener
//20140605 $Rep_diaAny = intval($APP_araMateix->format("z"));
//20140605 if($Rep_diaAny == 1){ //el dia 2
$Rep_diaMes = intval($APP_araMateix->format("j"));
 if($Rep_diaMes == 2){//el dia 2
//proves!!!!!!!if($Rep_diaMes == 6){//el dia 6//proves!!!!!!! 
//20140605 FINAL
    //possible informe anual
    $Rep_hora = intval($APP_araMateix->format("G"));
    if($Rep_hora == 8){
        $Rep_minut = intval($APP_araMateix->format("i"));
        if($Rep_minut > 40 && $Rep_minut < 50){ //el cron s'execute als quarts d'hora, i el volem a les 8:45
            //cal fer reports anuals
            $Rep_tipus = 3;
            include_once ('Repdc_common.php');
            include('Repdc_do.php');

        }
    }
    
}
//20140409 FINAL

//20181204 Neteja Duplicats //20220105 comento perque no funciona, $APP_BdD->OpenRs($sql) torna true o false, no l'array. el víctor esta actuant sobre això precisament ara
//
//$sql = "SELECT `when`,`idBooth`,`idDongle`,`str1`,`str2`,`typeInfo`,count(*),min(`db_time`),max(`db_time`) FROM `App_info`  GROUP BY `when`, `idBooth`, `idDongle`,`str1`,`str2`  HAVING count(*) > 1 LIMIT 30";
//$duplicates = $APP_BdD->OpenRs($sql);
//foreach ($duplicates as $dup) {
//    $sql = "SELECT * FROM `App_info` WHERE `db_time`>$dup[7] AND `when`=$dup[0]";
//    $llista = $APP_BdD->OpenRs($sql);
//    $sql_insert = "START TRANSACTION; INSERT INTO `App_info_duplicates`(`idInfo`, `when`, `idBooth`, `typeInfo`, `money`, `money2`, `currency`, `stock`, `i1`, `i2`, `i3`, `i4`, `i5`, `str1`, `str2`, `PBnew`, `in1`, `in2`, `in3`, `in4`, `in5`, `in6`, `in7`, `in8`, `pbs_time`, `db_time`) VALUES";
//    foreach ($llista as $item) {
//        $sql_insert .= "($item[0]";
//        for ($x = 1; $x < count($item); $x++) {
//                $sql_insert .= ", $item[$x]";
//        }
//        $sql_insert .= ") ";
//    }
//    $sql_insert .= "DELETE FROM `App_info` WHERE `idInfo` IN (SELECT `idInfo` FROM `App_info_duplicates`)";
//
//    $moure = $APP_BdD->Execute($sql_insert);
//
//    if ($moure) {
//        $APP_BdD->Execute("COMMIT TRANSACTION;");
//    } else {
//        $APP_BdD->Execute("ROLLBACK TRANSACTION;");
//    }
//}


//20181204 Fi Neteja Duplicats

?>
