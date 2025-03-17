<?php
include '../../../sessio.php';
require_once G_PATH . '/common/global.php';
require_once G_PATH . '/common/conexio.php';
if (!(isset($_GET['download']) && $_GET['download'] === 'true')) {
    echo "<script src='js.js'></script>";
    echo "<script src='auditList.js'></script>";
}


//$files = glob('csv'); // obtiene todos los archivos
//foreach($files as $file){
//  if(is_file($file)) // si se trata de un archivo
//    unlink($file); // lo elimina
//}

//agafar id usuari
$USERID = $_SESSION['USERID'];
//agafa dades semana primer i ultim dia i les transforma en una data
$semana = isset($_GET['semana']) ? $_GET['semana'] : '';
$primDia = isset($_GET['primDia']) ? $_GET['primDia'] : '';
$ultDia = isset($_GET['ultDia']) ? $_GET['ultDia'] : '';
$title = isset($_GET['title']) ? $_GET['title'] : '';
$filename = "Audit".$USERID.$semana.".csv";

$archiu = fopen($filename, "w");
if (!$archiu) {
    die("No se pudo crear el archivo CSV");
}

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
$divendres = $ultDia." 23:59:59";

//escriu tituls de columna
fputs($archiu, "Serial Nr, Name, Location, Last Connection, week, Date, Total Money, Total Plays, Overpayment".PHP_EOL);

//agafa les dedes de les maquines
$CLD_CON->OpenRs("SELECT idBooth, name, serialnumber, location, lastConn
                FROM App_booths 
                WHERE owner = $USERID");

$idBooth = [];
$name = [];
$serialnumber = [];
$location = [];
$lastConn = [];

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

    $cash = 0;
    $creditCard = 0;
    $net = 0;
    $money = 0;
    $overpayment = 0;
    $typeinfo_60 = 0;

    if ($CLD_CON->FetchArray()) {    
        $cash = $CLD_CON->GetArrayField("cash") ?: 0; 
        $creditCard = $CLD_CON->GetArrayField("CreditCard") ?: 0; 
        $net = $CLD_CON->GetArrayField("net") ?: 0; 
        $money = $CLD_CON->GetArrayField("money") ?: 0;
        $overpayment = $cash + $creditCard + $net - $money;
        if($overpayment<0){
            
            $sumaOver = -$overpayment;
            $overpayment = 0; 
            $creditCard = $creditCard + $sumaOver;
        }
        $money = intval($cash) + intval($creditCard) + intval($net);
    }
    
    $CLD_CON->OpenRs("SELECT COUNT(typeInfo) as typeinfo, `when`,
                (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints
                FROM App_info
                WHERE idBooth = $id and typeInfo = 10 AND `when` > '$dilluns' AND `when` < '$divendres'");
    $prints = 0;
    $typeinfo = 0;
    $when = '';
    
    if ($CLD_CON->FetchArray()) {
        $prints = $CLD_CON->GetArrayField("prints") ?: 0;
        $typeinfo = $CLD_CON->GetArrayField("typeinfo") ?: 0;
        $typeinfo = $typeinfo - $typeinfo_60;
        
        $when = $CLD_CON->GetArrayField("when") ?: '';

        $nameEscaped = str_replace(',', ' ', $name[$i]);
        $locationEscaped = str_replace(',', ' ', $location[$i]);
        
        //escriu les dades recollides al csv
        fputs($archiu, "$serialnumber[$i], $nameEscaped, $locationEscaped, $lastConn[$i], $semana, $primDia - $ultDia, $money, $prints, $overpayment".PHP_EOL);
    }
    $i++;
}
fclose($archiu);
chmod($filename, 0774);

if (isset($_GET['download']) && $_GET['download'] === 'true') {
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    flush();
    readfile($filename);
    unlink($filename);
    exit;
} else {
    echo "Download successful";
    echo "<iframe width='1' height='1' frameborder='0' src='$filename'></iframe>";
}