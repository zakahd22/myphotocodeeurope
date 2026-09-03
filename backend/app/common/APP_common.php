<?php 

defined('G_PATH') or define("G_PATH", dirname (__FILE__) . "/../../");

//utilitats
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
//20140701 INICI
function APP_myDateAndTime(DateTime $data, $idioma = "en"){
    if(is_null($data)) return "-";
    switch($idioma){
        case "en":
         return $data->format("m-d-Y h:i A");
        break;
        case "de":
         return $data->format("d.m.Y H:i");
        break;
        default:
            return $data->format("m-d-Y h:i A");
//         return $data->format("d/m/Y H:i");
    }
}
//20140701 FINAL


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

//function APP_diasetmana($ndia)
//{
//    switch($ndia){
//        
//        case "1": return "Lunes";
//        case "2": return "Martes";
//        case "3": return "Miércoles";
//        case "4": return "Jueves";
//        case "5": return "Viernes";
//        case "6": return "Sábado";
//        case "7": return "Domingo";
//    
//    }
//}
        
function APP_netejaXML($input){

//20120418    $replace = array("&lt;", "&gt;", "&amp;", "&apos;");
//20121108    $search  = array("<", ">", "&", "'");
//20121108    $replace = array("(", ")", "&amp;", "&apos;");//20120418
    $search  = array("<", ">");//20120418
    $replace = array("(", ")");//20120418
    $final = str_replace($search, $replace, $input);
    return $final;

}

function APP_preparaXML($input){

    if(!$input) return "";//20121023
    //de moment només APP_netejaXML
    
 //   return APP_netejaXML($input);
    return APP_escapeXML(APP_netejaXML($input));

}

function APP_escapeXML($input){
    
    return $input;//20121108 (Ho demanen des d'Ontomobile)

    $ret = "";
    $myArray = str_split ( $input);


    foreach ( $myArray as $c){
        if(($c >= '0') && ($c <= '9')){ $ret.=$c; continue;}
        if(($c >= 'a') && ($c <= 'z')){ $ret.=$c; continue;}
        if(($c >= 'A') && ($c <= 'Z')){ $ret.=$c; continue;}
        if($c == ' ') { $ret.=$c; continue;}
        if($c == '&') { $ret.=$c; continue;}
        if($c == ';') { $ret.=$c; continue;}
        if($c == '(') { $ret.=$c; continue;}
        if($c == ')') { $ret.=$c; continue;}
//        if($c == ';') { $ret.=$c; continue;}
        if($c == '.') { $ret.=$c; continue;}
        if($c == '-') { $ret.=$c; continue;}
        if($c == "'") { $ret.=$c; continue;}

        $ret.=  "&#".ord($c).";";

//        $ret.=  "\\u00".dechex(ord($c));
    }

    return $ret;

//    $l = strlen($input);
//    for($i = 0; $i < $l;$i++){
//        if()
//    }
//
//    $search  = array("<", ">", "&", "'");
////20120418    $replace = array("&lt;", "&gt;", "&amp;", "&apos;");
//    $replace = array("(", ")", "&amp;", "&apos;");//20120418
//    $final = str_replace($search, $replace, $input);
//    return $final;

} 

function rndm32($len) {
    $base32_table = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z","2","3","4","5","6","7","8","9");
    $out = "";
    for($i=0;$i<$len;$i++){
        $out.= $base32_table[rand(0,31)];
    }
    return $out;
}
function APP_QRdo($id) {
//20121031    $codi = rndm32(12);
    $codi = $id.rndm32(12);//20121031
    APP_QRfile($id,$codi);
    return $codi;
}
function APP_QRfile($id,$codi) {
//20131028    $nomFitxer = "userqr/qr$id.png";
//20140331    $nomFitxer = "userqr/qr$id$codi.png";//20131028
    $nomFitxer = "userqr/qr$codi.png";//20140331

    require("phpqrcode.php");
//	define('QR_ECLEVEL_L', 0);
//	define('QR_ECLEVEL_M', 1);
//	define('QR_ECLEVEL_Q', 2);
//	define('QR_ECLEVEL_H', 3);

    $level = QR_ECLEVEL_H;
     $margin = 2;
    QRcode::png("QR-id:$codi", $nomFitxer, $level, 6, $margin);
}
function APP_curPageURL() {
 $pageURL = 'http';
 $ssl = $_SERVER["HTTPS"];
 if ($ssl) if($ssl != "off") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
//  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["ORIG_PATH_INFO"];
 } else {
//  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["ORIG_PATH_INFO"];
 }
 $scriptname = APP_curPageName();
 $len = strlen ($pageURL) - strlen ($scriptname);
 return substr($pageURL,0,$len);
}
function APP_curPageName() {
 return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

//20130424 INICI log
function APP_fesLog($text) 
{
    if(file_exists("logAPP.dat")){//202009
        
    if(filesize("logAPP.dat") > 5000000){
//20140708        copy( "logAPP.dat" , "logAPP.bak" );
//20150529        rename( "logAPP.dat" , "logAPP.bak" );//20140708    
        rename( "logAPP.dat" , "logAPP-".  rndm32(3).".bak" );//20150529    
        $fh = fopen("logAPP.dat", 'w');
    }
    else $fh = fopen("logAPP.dat", 'a');
    
    } else {$fh = fopen("logAPP.dat", 'w');}//202009
    
//20150529    fwrite($fh, $text."\r");
    $ara = new DateTime("now");//20150529 
    fwrite($fh, $ara->format("mdhis"). "\t$text\r");//20150529
    fclose($fh);
}

//20130424 FINAL log

function APP_fesLogDebbug($text, $filename) {
    
    if(file_exists($filename . ".dat")){//202009
    
    if(filesize($filename . ".dat") > 5000000){
        rename($filename . ".dat" , "$filename-".  rndm32(3).".bak" );
        $fh = fopen($filename . ".dat", 'w');
    }
    else $fh = fopen($filename . ".dat", 'a');
    
    } else {$fh = fopen($filename . ".dat", 'w');}//202009
    
    $ara = new DateTime("now");
    fwrite($fh, $ara->format("mdhis"). "\t$text\r");
    fclose($fh);
}


//201808 INICI
function APP_base64_decode_custom($input){
    $custom = 'AaBxCjDpE3FhGkH7IzJiKLtMNgOP5QfRSwTU-VWX1YZbcdelm8noqrsuvy02469/';
    $default = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    
    return base64_decode(strtr($input, $custom, $default));
}
//201808 FINAL

//202009 INICI
function APP_base64_decode_custom20($input){
    if(strlen($input) == 0) return "";
    $custom =  'AaBxCjDpE3FhGkH7IzJiKLtMNgOP5QfRSwTU-VWX1YZbcdelm8noqrsuvy02469_';
    $default = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    
    return base64_decode(strtr($input, $custom, $default));
}
function APP_base64_encode_custom20($input){
    if(strlen($input) == 0) return "";
    $custom =  'AaBxCjDpE3FhGkH7IzJiKLtMNgOP5QfRSwTU-VWX1YZbcdelm8noqrsuvy02469_';
    $default = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    
    return strtr(base64_encode($input), $default, $custom);
}
function APP_base64_decode_custom21($input){
    if(strlen($input) == 0) return "";
    $custom =  'vjDpE3FhGP5zJQfRkH7Ii_KLtAaBxM69NgOSwTU-VWbcdX1YZelm8nCosuy024qr';
    $default = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    
    return base64_decode(strtr($input, $custom, $default));
}
function APP_base64_encode_custom21($input){
    if(strlen($input) == 0) return "";
    $custom =  'vjDpE3FhGP5zJQfRkH7Ii_KLtAaBxM69NgOSwTU-VWbcdX1YZelm8nCosuy024qr';
    $default = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    
    return strtr(base64_encode($input), $default, $custom); 
}
