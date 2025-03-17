<?php
include '../../../sessio.php';
require_once G_PATH . '/common/global.php';
require_once G_PATH . '/common/conexio.php';
echo "<script src='js.js'></script>";
echo "<script src='auditList.js'></script>";


//$files = glob('csv'); // obtiene todos los archivos
//foreach($files as $file){
//  if(is_file($file)) // si se trata de un archivo
//    unlink($file); // lo elimina
//}

//agafar id usuari
$USERID = $_SESSION['USERID'];
//agafa dades semana primer i ultim dia i les transforma en una data
$semana = $_GET[semana];
$primDia = $_GET[primDia];
$ultDia = $_GET[ultDia];
$title = $_GET[title];
$archiu = fopen("Audit".$USERID.$semana.".csv", "w");

if($primDia == '0'){
    $primDia = substr($title, 0,10);
    $primDia = str_replace("/", "-", $primDia);
    $ultDia = substr($title, 13,10);
    $ultDia = str_replace("/", "-", $ultDia);
    $primDia = new DateTime($primDia);
    $ultDia = new DateTime($ultDia);
    $primDia = $primDia->format("Y-d-m");
    $ultDia = $ultDia->format("Y-d-m");
}else{
    $primDia = new DateTime($primDia);
    $ultDia = new DateTime($ultDia);
    $primDia = $primDia->format("Y-m-d");
    $ultDia = $ultDia->format("Y-m-d");
}

$dilluns = $primDia." 00:00:00";
$dilluns_seg = $dilluns;
$divendres=  $ultDia." 24:60:60";

//escriu tituls de columna
fputs($archiu, "Serial Nr, Name, Location, Last Connection, week, Date, Total Money, Total Plays, Overpayment".PHP_EOL);

//agafa les dedes de les maquines
$CLD_CON->OpenRs("SELECT idBooth, name, serialnumber, location, lastConn
                FROM App_booths 
                WHERE owner = $USERID");


while ($CLD_CON->FetchArray()) {
    $idBooth[] = $CLD_CON->GetArrayField("idBooth");  
    $name[] = $CLD_CON->GetArrayField("name");  
    $serialnumber[] = $CLD_CON->GetArrayField("serialnumber");
    $location[] = $CLD_CON->GetArrayField("location");   
    $lastConn[] = $CLD_CON->GetArrayField("lastConn");     
}

$i = 0;
foreach ($idBooth as $id) {
    //agafa el audits de cada mauina
    $CLD_CON->OpenRs("SELECT SUM(money) as money, SUM(money2) as money2, SUM(i3) as cash, SUM(i4) as CreditCard, SUM(i5) as net
                FROM App_info
                WHERE idBooth = $id and (typeInfo = 60 or typeInfo = 10) AND `when` > '$dilluns' AND `when` < '$divendres'");
    while ($CLD_CON->FetchArray()) {    
        $cash = $CLD_CON->GetArrayField("cash"); 
        $creditCard = $CLD_CON->GetArrayField("CreditCard"); 
        $net = $CLD_CON->GetArrayField("net"); 
        $money = $CLD_CON->GetArrayField("money");
        $overpayment = $cash + $creditCard + $net - $money;
        if($overpayment<0){
            
            $sumaOver = -$overpayment;
            $overpayment = 0; 
            $creditCard = $creditCard + $sumaOver;
        }
        $money = intval($cash) + intval($creditCard) + intval($net);
    }
    
    $CLD_CON->OpenRs("SELECT  COUNT(typeInfo) as typeinfo, `when`,
                (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints
                FROM App_info
                WHERE idBooth = $id and typeInfo = 10 AND `when` > '$dilluns' AND `when` < '$divendres'");

    while ($CLD_CON->FetchArray()) {
        $prints = $CLD_CON->GetArrayField("prints");
        $typeinfo = $CLD_CON->GetArrayField("typeinfo");
        $typeinfo = $typeinfo - $typeinfo_60;
        
        $when = $CLD_CON->GetArrayField("when");

        //escriu les dades recollides al csv
        fputs($archiu, "$serialnumber[$i], $name[$i], $location[$i], $lastConn[$i], $semana, $primDia - $ultDia, $money, $prints, $overpayment".PHP_EOL);
    }
    $i = $i + 1;
}
echo "Download successful";
chmod($archiu, 0774);
//copy("Audit".$USERID.$semana.".csv", "csv/Audit".$USERID.$semana.".csv");
echo "<iframe width='1' height='1' frameborder='0' src='Audit".$USERID.$semana.".csv'></iframe>";
//sleep(10);
//unlink("Audit".$USERID.$semana.".csv");
