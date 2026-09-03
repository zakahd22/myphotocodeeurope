<?php
/*crontab l'executa a les 4 matinada dels dilluns
 * 0 4 * * 1 nice /usr/bin/php5 -f /kunden/homepages/46/d399659235/htdocs/app/owner/cronTwilioCostCsv.php >> /kunden/homepages/46/d399659235/htdocs/app/owner/cronTwilioCostCsv.log
 */
require("../common/APP_BdD.php");
require("../common/APP_common.php");
//potser no cal, de moment no farem servir aquest controller
//require_once ("../../sections/statisticsReports/controller/StatisticsReportsController.php"); //a eliminar?
date_default_timezone_set("Europe/Madrid");
//
//error_reporting(E_ALL);//a eliminar
//ini_set('display_errors', 1);//a eliminar

/****************************************** 
* 20211213 21-D-07 Consum SMS i whatsapp
*/ 
$type = 'Y-m-d';
$today = date($type, mktime(date("h"), date("i"), date("s"), date("m"), date("d"), date("Y")));
$daysEndLastWeek          = date("w", mktime(date("h"), date("i"), date("s"), date("m"), date("d"), date("Y")));
$daysStartLastWeek        = ($daysEndLastWeek - 1) + 7;
$lastWeekMonday     = date($type, strtotime("-$daysStartLastWeek day", strtotime($today)));
$lastWeekSunday     = date($type, strtotime("-$daysEndLastWeek day", strtotime($today)));


$mail_cont = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='https://www.w3.org/1999/xhtml'>
        <head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
            <title>TWILIO COSTS DC</title>
                <style type='text/css'>body {background-color:#ffffff;}

                     div, p, a, li, td  { -webkit-text-size-adjust:none;}

                     a:visited {text-decoration:none;}
                     a:link {text-decoration:none;}
                     a:hover {text-decoration:none;}
                     a:active {text-decoration:none;}
                </style>
        </head>
<body>";
//de moment un email
//TODO: canviar per les copies a jtarres i a qui sigui...
$mail_email = "main@dc-image.com";
$mail_nom = "Digital Centre";
$mail_subject = "TWILIO COSTS FROM $lastWeekMonday TO $lastWeekSunday";
$mail_cont .= "<p>Good morning,<br>
You have two listings in this post with the costs that Twilio has incurred to send <b>SMS and whatsapp from $lastWeekMonday to $lastWeekSunday</b>:<br>
-1 Grouped by <b>PhotoBooth</b> from highest to lowest <b>weekly cost</b>.<br>
-1 Grouped by <b>owner</b> from highest to lowest <b>weekly cost</b>.<br>
</p>";




                    
                   
    $mail_replayto = "main@dc-image.com";

    $mail_copia = "eloi@dc-image.com";

                        $mail_remitent = "main@dc-image.com";//20150626
                        $mail_nomremitent = "DC REPORTS";

//                        $mail_copia1 = "accounts@dc-image.com";
//                        $mail_copianom1 = "Accounts DC";
//                        $mail_copia2 = "support@dc-image.com";
//                        $mail_copianom2 = "Support DC";
//20150625location                        $mail_subject = "Alert Detection Notification"; 
                        

          
/**********************************************************************************
 * 1.- Comencem csv cosum ordenat per major consum i agrupat per PB
 */

//$filename1 = "AuditConsumPBs.csv";
//$filePath1 = "/kunden/homepages/46/d399659235/htdocs/app/owner/AuditConsumPBs.csv"; 
//no podem fer servir variables $_SERVER desde la versió CLI de PHP, només a CGI, la que s'executa desde el navegador
//$_SERVER["DOCUMENT_ROOT"]."/app/owner/AuditConsumPBs.csv"; //--> nomes funciona desde navegador, desde crontab no envia els adjunts

//definim els anys
$year = date("Y");
$year1 = strtotime ( '-1 year' , strtotime($year ));
$year1 =  date("Y", $year1);
$year2 = strtotime ( '-2 year' , strtotime ( $year ));
$year2 =  date("Y", $year2);




/***
 * Afegim sumatoris anuals dels 3 ultims anys 
 * 
 */

$arrayCostBooth = Array();
$arrayCostOwner = Array();

/****
 * idBooth cost
 */
//$year
$APP_BdD->OpenRs("SELECT g.idBooth as gidBooth, ab.name as abname , g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
LEFT JOIN App_booths ab ON ab.idBooth=g.idBooth
WHERE g.state=6 AND g.idBooth!=0  AND YEAR(`last`) = $year GROUP BY g.idBooth ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $idBooth = $APP_BdD->GetArrayField("gidBooth");      
    $cost = $APP_BdD->GetArrayField("costTwilio");  
    $arrayCostBooth[$year][$idBooth]   =   $cost;
} 

//$year1
$APP_BdD->OpenRs("SELECT g.idBooth as gidBooth, ab.name as abname , g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
LEFT JOIN App_booths ab ON ab.idBooth=g.idBooth
WHERE g.state=6 AND g.idBooth!=0  AND YEAR(`last`) = $year1 GROUP BY g.idBooth ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $idBooth = $APP_BdD->GetArrayField("gidBooth");      
    $cost = $APP_BdD->GetArrayField("costTwilio");  
    $arrayCostBooth[$year1][$idBooth]   =   $cost;
} 

//$year2
$APP_BdD->OpenRs("SELECT g.idBooth as gidBooth, ab.name as abname , g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
LEFT JOIN App_booths ab ON ab.idBooth=g.idBooth
WHERE g.state=6 AND g.idBooth!=0  AND YEAR(`last`) = $year2 GROUP BY g.idBooth ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $idBooth = $APP_BdD->GetArrayField("gidBooth");      
    $cost = $APP_BdD->GetArrayField("costTwilio");  
    $arrayCostBooth[$year2][$idBooth]   =   $cost;
} 

/****
 * Owner cost
 */

//$year
$APP_BdD->OpenRs("SELECT g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
WHERE g.state=6 AND g.owner!=0  AND YEAR(`last`) = $year GROUP BY gowner ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $ownerId = $APP_BdD->GetArrayField("gowner");      
    $cost = $APP_BdD->GetArrayField("costTwilio");  
    $arrayCostOwner[$year][$ownerId]   =   $cost;
} 

//$year1
$APP_BdD->OpenRs("SELECT g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
WHERE g.state=6 AND g.owner!=0  AND YEAR(`last`) = $year1 GROUP BY gowner ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $ownerId = $APP_BdD->GetArrayField("gowner");      
    $cost = $APP_BdD->GetArrayField("costTwilio");  
    $arrayCostOwner[$year1][$ownerId]   =   $cost;
} 

//$year2
$APP_BdD->OpenRs("SELECT g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
WHERE g.state=6 AND g.owner!=0  AND YEAR(`last`) = $year2 GROUP BY gowner ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $ownerId = $APP_BdD->GetArrayField("gowner");      
    $cost = $APP_BdD->GetArrayField("costTwilio");  
    $arrayCostOwner[$year2][$ownerId]   =   $cost;
}






$arxiu1 = fopen($filename1, "w");  
$mail_cont .= "<br></br>"; 
$mail_cont .= '<table border="1" style = "border-color: #96D4D4; border-collapse: collapse; padding: 10px;">';



//Afegi els usuaris banejats, si n'hi ha

$sql_banned = "SELECT  r.`id` as rid , r.`name` rname FROM CLD_Login l, rentals r WHERE r.`id` = l.`id_user` AND banned=1";

$APP_BdD->OpenRs($sql_banned);
$i=0;
while ($APP_BdD->FetchArray()) {
    if($i==0){
//        fputs($arxiu1, ", BANNED USERS, ,,".PHP_EOL);
        $mail_cont .= "<tr><td colspan='8'>BANNED USERS</td></tr>";        
    }
    $rid = $APP_BdD->GetArrayField("rid");
    $rname = $APP_BdD->GetArrayField("rname");
//    fputs($arxiu1, '"'.$rid.'", "'.$rname.'", ,,'.PHP_EOL);
    $mail_cont .= "<tr><td>$rid</td><tdc olspan='4'>$rname></td><td colspan='3'></td></tr>";
    $i++;
    $mail_cont .= "<tr><td colspan='8'></td></tr>";    
}  

//fputs($arxiu1, ",  , , , ".PHP_EOL);





//escriu titols de columna 
//fputs($arxiu1, ", BOOTH TWILIO COSTS:, ,FROM $lastWeekMonday TO $lastWeekSunday,".PHP_EOL);
$mail_cont .= "<tr><th colspan='8'; style = 'text-align: center;'>BOOTH TWILIO COSTS</th></tr>";
//fputs($arxiu1, ",  , , , ".PHP_EOL);
//$mail_cont .= "<tr><td colspan='8'></td></tr>";
//fputs($arxiu1, "idBooth, Booth Name , Owner Id, Owner Name , Twilio Costs SMS + Whatsapp".PHP_EOL);
$mail_cont .= "<tr><th style = 'padding: 5px;'>idBooth</th><th style = 'padding: 5px;'>Booth Name</th><th style = 'padding: 5px;'>Owner Id</th><th style = 'padding: 5px;'>Owner Name</th><th style = 'padding: 5px;'>FROM $lastWeekMonday TO $lastWeekSunday</th><th style = 'padding: 5px;'>$year</th><th style = 'padding: 5px;'>$year1</th><th style = 'padding: 5px;'>$year2</th></tr>";
//agafa les dades de gestor
$APP_BdD->OpenRs("SELECT g.idBooth as gidBooth, ab.name as abname , g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
LEFT JOIN App_booths ab ON ab.idBooth=g.idBooth
WHERE g.state=6 AND g.idBooth!=0  AND `last` >= DATE('$lastWeekMonday') AND `last` < (DATE('$lastWeekSunday') + INTERVAL 1 DAY) GROUP BY g.idBooth ORDER BY costTwilio DESC");

while ($APP_BdD->FetchArray()) {
    $idBooth = $APP_BdD->GetArrayField("gidBooth");  
    $boothName = $APP_BdD->GetArrayField("abname");  
    $ownerId = $APP_BdD->GetArrayField("gowner");
    $ownerName = $APP_BdD->GetArrayField("rname");   
    $cost = $APP_BdD->GetArrayField("costTwilio");      

if(!isset($arrayCostBooth[$year][$idBooth]))$arrayCostBooth[$year][$idBooth] = 0;
if(!isset($arrayCostBooth[$year1][$idBooth]))$arrayCostBooth[$year1][$idBooth] = 0;
if(!isset($arrayCostBooth[$year2][$idBooth]))$arrayCostBooth[$year2][$idBooth] = 0;
    
//    fputs($arxiu1, '"'.$idBooth.'", "'.$boothName.'", "'.$ownerId.'", "'.$ownerName.'", "'.$cost.'"'.PHP_EOL);
    $mail_cont .= "<tr><td style = 'text-align: right; padding: 3px;'>$idBooth</td><td style = 'padding: 3px;'>$boothName</td><td style = 'text-align: right; padding: 3px;'>$ownerId</td><td style = 'padding: 3px;'>$ownerName</td><td style = 'text-align: right; padding: 3px;'>$cost</td><td style = 'text-align: right; padding: 5px;'>".$arrayCostBooth[$year][$idBooth]."</td><td style = 'text-align: right; padding: 5px;'>".$arrayCostBooth[$year1][$idBooth]."</td><td style = 'text-align: right; padding: 5px;'>".$arrayCostBooth[$year2][$idBooth]."</td></tr>";
}     
     




$mail_cont .= "</table>";
$mail_cont .= "<br></br>";
$mail_cont .= "<br></br>";

     
/**********************************************************************************
 * 2.- Comencem csv cosum ordenat per major consum i agrupat per owner
 */
//$filename2 = "AuditConsumOwners.csv";
//$filePath2 = "/kunden/homepages/46/d399659235/htdocs/app/owner/AuditConsumOwners.csv"; 
//$arxiu2 = fopen($filename2, "w");  
$mail_cont .= '<table border="1" style = "border-color: #96D4D4; border-collapse: collapse;">';
//Afegi els usuaris banejats, si n'hi ha


$sql_banned = "SELECT  r.`id` as rid , r.`name` rname FROM CLD_Login l, rentals r WHERE r.`id` = l.`id_user` AND banned=1";

$APP_BdD->OpenRs($sql_banned);
$i=0;
while ($APP_BdD->FetchArray()) {
    if($i==0){
//        fputs($arxiu2, ", BANNED USERS, ,,".PHP_EOL);
        $mail_cont .= "<tr><td colspan='6'>BANNED USERS</td></tr>";
    }
    $rid = $APP_BdD->GetArrayField("rid");
    $rname = $APP_BdD->GetArrayField("rname");
//    fputs($arxiu2, '"'.$rid.'", "'.$rname.'", ,,'.PHP_EOL);
    $mail_cont .= "<tr><td>$rid</td><td colspan='2'>$rname></td><td colspan='3'></td></tr>";
    $i++;
}  

//fputs($arxiu2, ",  , , , ".PHP_EOL);
//$mail_cont .= "<tr><td colspan='6'></td></tr>";

//escriu titols de columna 
//fputs($arxiu2, ",OWNER TWILIO COSTS:,  FROM $lastWeekMonday TO $lastWeekSunday".PHP_EOL);
$mail_cont .= "<tr><th colspan='6'; style = 'text-align: center;'>OWNER TWILIO COSTS</th></tr>";
//fputs($arxiu2, ",,".PHP_EOL);
//$mail_cont .= "<tr><td colspan='6'></td></tr>";
//fputs($arxiu2, "Owner Id, Owner Name , Twilio Costs SMS + Whatsapp".PHP_EOL);
$mail_cont .= "<tr><th style = 'padding: 5px;'>ownerId</th><th style = 'padding: 5px;'>Owner Name</th><th style = 'padding: 5px;'>FROM $lastWeekMonday TO $lastWeekSunday</th><th style = 'padding: 5px;'>$year</th><th style = 'padding: 5px;'>$year1</th><th style = 'padding: 5px;'>$year2</th></tr>";
//agafa les dades de gestor
$APP_BdD->OpenRs("SELECT g.owner as gowner, r.name as rname, SUM(g.cost) as costTwilio FROM `gestor` g
LEFT JOIN rentals r ON r.id=g.owner 
WHERE g.state=6 AND g.owner!=0  AND `last` >= DATE('$lastWeekMonday') AND `last` < (DATE('$lastWeekSunday') + INTERVAL 1 DAY) GROUP BY gowner ORDER BY costTwilio DESC");
  
while ($APP_BdD->FetchArray()) {
    
    $ownerId = $APP_BdD->GetArrayField("gowner");
    $ownerName = $APP_BdD->GetArrayField("rname");   
    $cost = $APP_BdD->GetArrayField("costTwilio");      
    
if(!isset($arrayCostOwner[$year][$ownerId]))$arrayCostOwner[$year][$ownerId] = 0;     
if(!isset($arrayCostOwner[$year1][$ownerId]))$arrayCostOwner[$year1][$ownerId] = 0; 
if(!isset($arrayCostOwner[$year2][$ownerId]))$arrayCostOwner[$year2][$ownerId] = 0; 

 //   fputs($arxiu2, '"'.$ownerId.'", "'.$ownerName.'", "'.$cost.'"'.PHP_EOL);
    $mail_cont .= "<tr><td style = 'text-align: right; padding: 5px;'>$ownerId</td><td style = 'padding: 5px;'>$ownerName</td><td style = 'text-align: right; padding: 5px;'>$cost</td><td style = 'text-align: right; padding: 5px;'>".$arrayCostOwner[$year][$ownerId]."</td><td style = 'text-align: right; padding: 5px;'>".$arrayCostOwner[$year1][$ownerId]."</td><td style = 'text-align: right; padding: 5px;'>".$arrayCostOwner[$year2][$ownerId]."</td></tr>";
}               
$mail_cont .= "</table>";                         
                         
include("../common/APP_mail_attachment.php");

?>
