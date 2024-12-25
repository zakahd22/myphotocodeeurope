<?php

class utils {
    static function get_rndm32($len) {
        $base32_table = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z","2","3","4","5","6","7","8","9");
        $out = "";
        for($i=0;$i<$len;$i++){
            $out .= $base32_table[rand(0,31)];
        }
        return $out;
    }

    static function get_rndm64($len) {
        $base64_table = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9","+");
        $max = count($base64_table) - 1;
        $out = "";
        for($i=0;$i<$len;$i++){
            $out .= $base64_table[rand(0,$max)];
        }
        return $out;
    }
    
    static function get_percent($value, $totalValue){
        $response = round(($value *100)/$totalValue, 2);
        
        return $response;
    }

    static function vd($text) {
        echo '<pre>';
        var_dump($text);
        echo '</pre>';
    }

    static function echo_($content, $html = false) {
        $content = $content . ($html? " <br />":" \n");
        echo $content;
    }
    
    static function log($text, $file, $trace='logGlb', $jump=true) {
        $file= G_PATH . "log/". $file;
        if(filesize($file . ".dat") > 5000000) {
            rename( $file . ".dat", $file . "." . utils::get_rndm32(3) . ".bak" );    
            $fh = fopen($file . ".dat", 'w');
        }
        else { 
            $fh = fopen($file . ".dat", 'a');             
        }
        try {
            if ($jump) {
                fwrite($fh, date('Y-m-d H:i:s ') . 'TRACE ' . $trace . ': ' . var_export($text, true) . "\r\n");
            }
            else {
                fwrite($fh, var_export($text, true));                   
            }
        } catch (Exception $e) {
            fwrite("\r\n" . $fh, date('Y-m-d H:i:s ') . 'TRACE ' . $trace . ': ' . $e . "\r\n");
        }
        /*finally {
            fclose($fh);
        }*/
    }
    
    static function rm_log($file){
        unlink(G_PATH . "log/". $file . ".dat");
    }

    static function is_test(){
        return (G_TEST == 1);
    }
    
    static function get_datetime($format='Y-m-d H:i:s', $dt_time=false) {
        return $dt_time? date($format, $dt_time) : date($format);
    }

    static function get_date_std($format='YmdHis', $std_time=false) {
        return $std_time? date($format, $std_time) : date($format);
    }

    static function increase_date_std($std_time,$d=0,$h=0,$i=0,$format='YmdHis') {
        $date = DateTime::createFromFormat($format, $std_time);
        $time = strtotime($date->format('Y-m-d H:i:s')) + (($d*24+$h)*60+$i)*60;
        return utils::get_date_std($format,$time);
    }

    static function format_date_std($datetime, $y=1,$m=1,$d=1,$h=0,$i=0,$s=0, $std_format='YmdHis') {
        $timestamp = DateTime::createFromFormat($std_format, $datetime);
        
        $y = $y? 'Y':'';
        $m = $m? 'm/':'';
        $d = $d? 'd/':'';
        $h = $h? 'H':'';
        $i = $i? ':i':'';
        $s = $s? ':s':'';
        
        switch ($_SESSION['LANG']) {
            case 'es':
                $dp_format = $d.$m.$y . ' ' . $h.$i.$s;
                break;
            case 'en-US':
                $dp_format = $m.$d.$y . ' ' . $h.$i.$s;
                break;
            default :
                $dp_format = $m.$d.$y . ' ' . $h.$i.$s;
                break;                
        }
        return $timestamp->format($dp_format);
    }

    static function datepicker_to_date_std($date_std, $dt_format='Ymd', $std_format = 'YmdHis') {        
        return $date_std . "120000";
    }

    static function date_std_to_datepicker($datetime) {
        $dp_format = 'Ymd';
        $dt_format='YmdHis';
        $datepicker = DateTime::createFromFormat($dt_format, $datetime);
        return $datepicker->format($dp_format);
    }

    static function datetime_to_date_std($datetime, $dt_format='Y-m-d H:i:s', $std_format = 'YmdHis') {
        $date_std = DateTime::createFromFormat($dt_format, $datetime);
        return $date_std->format($std_format);
    }

    static function date_std_to_datetime($date_std, $dt_format='Y-m-d H:i:s', $std_format = 'YmdHis') {
//        var_dump($date_std);
        $datetime = DateTime::createFromFormat($std_format, $date_std);
        return $datetime->format($dt_format);
    }

    static function diff_datetime($datetime_1, $datetime_2, $format='Y-m-d H:i:s') {
        $seconds = strtotime($datetime_1) - strtotime($datetime_2);
        return $seconds;
    }

    static function diff_date_std($date_std_1, $date_std_2, $dt_format='Y-m-d H:i:s') {
        $datetime_1 = utils::date_std_to_datetime($date_std_1, $dt_format); 
        $datetime_2 = utils::date_std_to_datetime($date_std_2, $dt_format);
        $seconds = utils::diff_datetime($datetime_1, $datetime_2, $dt_format);
        return $seconds;
    }    
    
    static function modify_date($date_std, $operation, $std_format='YmdHis'){
        $date = DateTime::createFromFormat($std_format, $date_std);
        $date = $date->modify($operation);
        $date = $date->format($std_format);
        
        return $date;
    }

    static function get_IntDate($date){
        return intval(substr($date,0,4)."".substr($date,5,2)."".substr($date,8,2));
    }
        // NO S'UTILITZEN //
    static function increase_datetime($datetime,$days=0,$hours=0,$minutes=0,$format='Y-m-d H:i:s') {
        $time = strtotime($datetime) + (($days*24+$hours)*60+$minutes)*60;
        return utils::get_datetime($time=$time,$format=$format);
    }
    static function datepicker_to_datetime($datepicker, $dp_format = 'Ymd', $dt_format='Y-m-d H:i:s') {
        $datetime = DateTime::createFromFormat($dp_format, $datepicker);
        return $datetime->format($dt_format);
    }
    static function datetime_to_datepicker($datetime) {
        $dp_format = 'Ymd';
        $dt_format='Y-m-d H:i:s';
        $datepicker = DateTime::createFromFormat($dt_format, $datetime);
        return $datepicker->format($dp_format);
    }

    static function echo_price($price, $currency=840) {
        switch ($currency) {
            case 840:
                return '$'.$price;
                break;
            case 978:
                return $price.'€';
                break;
            default:
                break;
        }
    }    

    static function echo_currency($currency=840) {
        switch ($currency) {
            case 840:
                echo '$';
                break;
            case 978:
                echo '€';
                break;
            default:
                break;
        }
    }   
    
    static function motor_conect($metode,$data){
        $a = G_CTRL_DEV_PAGE;
//        utils::log("TRACE 0.0 {$a}", G_PATH . "log/logCTRLConnect", "utils::motor_conect");

        $url_array = array();
        if(defined('G_CTRL_DEV_PAGE')){ 
//            utils::log("TRACE 0.1", G_PATH . "log/logCTRLConnect", "utils::motor_conect");
            array_push($url_array, G_CTRL_DEV_PAGE . "{$metode}");
        }
        if(defined('G_CTRL_ES_PAGE')){
//            utils::log("TRACE 0.2", G_PATH . "log/logCTRLConnect", "utils::motor_conect");
            array_push($url_array, G_CTRL_ES_PAGE . "{$metode}");
        }
//        if(defined('G_CTRL_US_PAGE')) array_push($url_array, G_CTRL_US_PAGE . "{$metode}");

//        utils::log($url_array, G_PATH . "log/logCTRLConnect", "utils::motor_conect");
        
        $req = $data;
        $res = array();
        foreach ($url_array as $url){
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    //        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, 0);
    //        curl_setopt($ch, CURLOPT_ENCODING, '');

            $single_res = array('url'=>$url, 'res'=> curl_exec($ch));
            if($single_res['res'] == false){ 
                utils::log("Failed to connect to {$single_res['url']}", G_PATH . "log/logCTRLConnect", "utils::motor_conect");
            }
            
            array_push($res, $single_res);
            curl_close($ch);
        }
        
        return $res;
    }
    
    static function putCurrency($import, $simbol, $position){
        if(strlen($import) == 0){
            $strimport = "0";
        }
        else{
            $strimport = intval($import);
        }
        switch($position){
            default:
            case 0:
                $value = "$strimport$simbol";
                break;
            
            case 1:
                $value = $simbol.$strimport;
                break;
        }
        
        return $value;
    }
    
    static function putCurrencyFloat($import, $simbol, $position){
        if(strlen($import) == 0){
            $strimport = "0";
        }
        else{
            //$strimport = intval($import);
            $strimport = number_format($import,4);
        }
        switch($position){
            default:
            case 0:
                $value = "$strimport$simbol";
                break;
            
            case 1:
                $value = $simbol.$strimport;
                break;
        }
        
        return $value;
    }
    
    static function sumHours($h1, $m1, $h2, $m2){
        $sumHours = 0;
        $sumMinutes = 0;
        
        $sumHours = $h1 + $h2;
        $sumMinutes = $m1 + $m2;
        
        $minutes_to_hours = intval($sumMinutes/60);
        if($minutes_to_hours >= 1){
            $sumHours += $minutes_to_hours;
            $sumMinutes = $sumMinutes - (60 * $minutes_to_hours);
        }  
        
        return array($sumHours, $sumMinutes);
    }
    
    static function printHours($h, $m){
        return "{$h}:" . (($m < 10)? "0{$m}" : "{$m}");
    }
}

class maps {
    public $step_x;
    public $step_y;
    
    function __construct($step_x, $step_y) {
        $this->step_x = $step_x;
        $this->step_y = $step_y;
        utils::log('Init log cities', 'logCities', $jump=true);    
    }


//    public function getCities($start_x, $end_x, $start_y, $end_y) {
//        utils::log('getting cities', 'logCities', $jump=true);    
//        $result = array();
//        for ($x=$start_x; $x<$end_x; $x+=$this->step_x) {
//            for ($y=$start_y; $y>$end_y; $y-=$this->step_y) { 
//        //        $result[] = getAddress_ob($x,$y)[0]->address_components[2]->long_name;
//                $var = $this->getAddress_ob($x,$y)[0]->address_components;
//        //        $result[$var[2]->long_name] = $result[$var[2]->long_name] + 1;
//
//                foreach($var as $e){ 
//        //                 if(isset($e['types']) && in_array("street_number",$e['types'])) $addr['street_number'] = $e['long_name']; 
//        //                 if(isset($e['types']) && in_array("street_number",$e['types'])) $addr['route'] = $e['long_name']; 
//                    if(isset($e->types) && in_array("administrative_area_level_2", $e->types)) {
//                        $result[$e->long_name] += 1; 
//                    }
//                } 
//        //        break;
//            }
//        //    break;
//        }
//        return $result;        
//    }
    
    static function getCoordinates($address){
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
        $response = file_get_contents($url);
        //var_dump($response);
        $json = json_decode($response,TRUE); //generate array object from the response from the web
        //var_dump($json);
        //$street = $json['results'][0]['address_components'][1];
        //$postal_code = $json['results'][0]['address_components'][6];
        //var_dump($street);
        //echo('<br>');
        //var_dump($postal_code);
        //var_dump($json);
        $coordinates = array(
            $json['results'][0]['geometry']['location']['lat'],
            $json['results'][0]['geometry']['location']['lng']
        );
        return json_encode($coordinates);
    }
    
    static function blurCoordinates($lat,$lng){
        $blur_lat = rand(($lat*1e5)-500,($lat*1e5)+500)/1e5;
        $blur_lng = rand(($lng*1e5)-500,($lng*1e5)+500)/1e5;
        return(array($blur_lat,$blur_lng));
    }
    
    static function getAddress($lat, $lon){
        $url  = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".
             $lat.",".$lon."&sensor=false";
        $json = @file_get_contents($url);
        //var_dump($json);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            $address = $data->results[0]->formatted_address;
           }
        return $address;
    }

    static function getAddress_ob($lat, $lon){
        $url  = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".
             $lat.",".$lon."&sensor=false";
        $json = @file_get_contents($url);
        //var_dump($json);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            $address = $data->results;
           }
        return $address;
    }
    
}
class logging {
    private $file;
    private $dir;
    
    function __construct($file='logGlb', $dir='log/log') {
        $this->file = basename($file, '.php');
        $this->dir = $dir;
//        $this->add('Log Init.');
    }

    function add($text, $jump=true) {
        if(filesize($this->dir . ".dat") > 5000000) {
            rename( $this->dir . ".dat", $this->dir . "." . utils::get_rndm32(3) . ".bak" );    
            $fh = fopen($this->dir . ".dat", 'w');
        }
        else { 
            $fh = fopen($this->dir . ".dat", 'a');             
        }
        try {
            if ($jump) {
                fwrite($fh, date('Y-m-d H:i:s ') . 'TRACE ' . $this->file . ': ' . var_export($text, true) . "\r\n");
            }
            else {
                fwrite($fh, date('Y-m-d H:i:s ') . ': ' . var_export($text, true));                   
            }
        } catch (Exception $e) {
            fwrite("\r\n" . $fh, date('Y-m-d H:i:s ') . 'TRACE ' . $this->file . ': ' . $e . "\r\n");
        }

        fclose($fh);
    }
}
?>
