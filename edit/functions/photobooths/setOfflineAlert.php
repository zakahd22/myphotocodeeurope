<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$timeZone = $_POST['timeZone'];
$OnOff = $_POST['active'];
$hs = $_POST['hs'];
$he = $_POST['he'];
$ms = $_POST['ms'];
$me = $_POST['me'];


//20150704 INICI
//codi anterior:
//if($he<$hs){
//    $midNight = 1;
//}else{
//    $midNight = 0;
//}
//   if ($CLD_CON->Execute("UPDATE App_booths SET hs=$hs , he=$he , ms=$ms , me=$me , timeZone='$timeZone' , midnight=$midNight , alertOffline=$OnOff WHERE idBooth =$ID")) {
//        echo "OK";
//    } else {
//        echo "ERROR";
//    }

//temps segons el timeZone del server, primer fixem segons el del PB
  //problemes a conf.php!!!!!      $ara = new DateTime("now"); $serverTzone = $ara->getTimezone();//time zone del server
        //
        $serverTzone = new DateTimeZone("America/New_York");
        $araStart = new DateTime("now",new DateTimeZone($timeZone));//time zone del PB
        $araStart->setTime($hs, $ms, 0);
        $araStart->setTimezone($serverTzone);
        $hmS = $araStart->format("'Hi'");
        
        $araEnd = new DateTime("now",new DateTimeZone($timeZone));//time zone del PB
        $araEnd->setTime($he, $me, 0);
        $araEnd->setTimezone($serverTzone);
        $hmE = $araEnd->format("'Hi'");
        
  
        if($hmE > $hmS){
            $midNight = 0;
        }
        else{
            $midNight = 1;
        }


    
   if ($CLD_CON->Execute("UPDATE App_booths SET hS=$hs , hE=$he , mS=$ms , mE=$me , timeZone='$timeZone',  hmS=$hmS, hmE=$hmE , midnight=$midNight , alertOffline=$OnOff WHERE idBooth =$ID")) {
        echo "OK";
    } else {
        echo "ERROR";
    }
    
//20150704 FINAL


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
