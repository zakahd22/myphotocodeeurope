<?php
require_once dirname(__FILE__) . "/../common/global.php";
include G_PATH."common/conexio.php";

if(!$esLogin){

    if (isset($_SESSION['EVUPid']))  $EVUPid = $_SESSION['EVUPid']; 
    else{
       echo "ko#notLogged"; return; 

    }
}

//require("APP_BdD.php");
include_once "../common/conexio.php";
$APP_BdD = $CLD_CON;

function VIC_fesLog($text) 
{
    if(filesize("EVUPlogVIC.dat") > 5000000){
        copy( "EVUPlogVIC.dat" , "logVIC.bak" );
        $fh = fopen("EVUPlogVIC.dat", 'w');
    }
    else $fh = fopen("EVUPlogVIC.dat", 'a');
//    fwrite($fh, $text);
    fwrite($fh, date('Y-m-d H:i:s ') . $text . "\n");
    fclose($fh);
}
        
        
//utilitats de APP_common
function APP_myDate(DateTime $data, $idioma = "en"){
    if(is_null($data)) return "-";
    if(strlen($idioma)==0) $idioma = $ABCH_idioma;
    switch($idioma){
        case "en":
         return $data->format("m-d-Y");
        break;
        case "de":
         return $data->format("d.m.Y");
        break;
        default:
         return $data->format("d/m/Y");
    }
}


function APP_myDateStr($aaaammdd, $idioma = "en"){

    if(strlen($aaaammdd)!=8) return "-";
    switch($idioma){
        case "en":
         return substr($aaaammdd,4,2)."-".substr($aaaammdd,6,2)."-".substr($aaaammdd,0,4);
        break;
        case "de":
         return substr($aaaammdd,6,2).".".substr($aaaammdd,4,2).".".substr($aaaammdd,0,4);
        break;
        default:
         return substr($aaaammdd,6,2)."/".substr($aaaammdd,4,2)."/".substr($aaaammdd,0,4);
    }
}
?>
