<?php
    //ini_set('meemory_limit', '96M');
    //ini_set('post_max_size', '64M');
    //ini_set('upload_max_filesize', '64M');
//    require 'config/params.php';

    connect();
    $doTrace = false;

    function connect(){
        require 'config/config.php';

        $link = mysql_connect($DB_myphotocode_web['host'],$DB_myphotocode_web['user'],$DB_myphotocode_web['pass']);
        if (!$link) die('ko');
        mysql_select_db($DB_myphotocode_web['database']);
    }

//
//
//    function checkCode($code){
//        $photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$code'"));
//        if ($photo){
//            $event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$photo[event_id]"));
//
//            $path = "events/".$event['start_date'].$event['id'];
//
//            $file1 = $path."/".$code.".jpg";
//            $file2 = "../".$path."/".$code.".jpg";
//            $file3 = "../../".$path."/".$code.".jpg";
//            $file4 = "../../../".$path."/".$code.".jpg";
//
//            if (file_exists($file1) || file_exists($file2) || file_exists($file3) || file_exists($file4)){
//                return true;
//            }
//            else{
//                return false;
//            }		
//        }
//        else{
//            return false;
//        }
//    }

    function clearBoth($height){
        echo "<div style='clear:both;height:".$height."px;'><img src='assets/images/blank.gif' width='1' height='1' /></div>";
    }

    function trace($string){
        global $doTrace;
        if ($doTrace) echo "<span class='trace'>".$string."</span>";
    }

    function date8($init,$separator){
        $day = (int)substr($init,6,2);
        $month = (int)substr($init,4,2);
        $year = (int)substr($init,0,4);
        return $year.$separator.$month.$separator.$day;
    }		
?>