<?php
require("../common/APP_BdD.php");

//TRACE 
//var_dump($_REQUEST);


//per a modificar coses
if(isset($_REQUEST['m'])){ $accio = $_REQUEST['m'];}

switch($accio){
    case 1://insertar News
        if(isset($_REQUEST['t'])){ $type = $_REQUEST['t'];} else $type = 1;
        if(isset($_REQUEST['url'])){ $url = $_REQUEST['url'];} else{echo "KO - no url"; return;}
        if(isset($_REQUEST['urlMobile'])){ $urlMobile = $_REQUEST['urlMobile'];} else{echo "KO - no urlMobile";  return;}
        $sql = "INSERT INTO  `App_news` (`url`, `urlMobile`,estat,type) VALUES ('$url','$urlMobile',1,$type); ";
//echo "<br/>TRACE $sql";//TRACE
        $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
        break;
//    case 2://insertar o modificar Photobooths
//        if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];} else return;
//        if(isset($_REQUEST['name'])){ $name = $_REQUEST['name'];} else return;
//        if(isset($_REQUEST['latitude'])){ $latitude = $_REQUEST['latitude']; $latitude*=1000000;} else return;
//        if(isset($_REQUEST['longitude'])){ $longitude = $_REQUEST['longitude']; $longitude*=1000000;} else return;
//        if(isset($_REQUEST['location'])){ $location = $_REQUEST['location'];} else return;
//        if(!$idBooth){//insert
//            //SELECT `estat`, `type`, `owner`, `name`,`hS`, `mS`, `hE`, `mE` FROM `App_booths` WHERE 1
////char to bigint            $sql = "INSERT INTO  `App_booths` (`name`, `type`, `owner`,`latitude`, `longitude`, `location`) VALUES ('$name', 'A', 7, '$latitude','$longitude','$location'); ";
//            $sql = "INSERT INTO  `App_booths` (`name`, `type`, `owner`,`latitude`, `longitude`, `location`) VALUES ('$name', 'A', 7, $latitude,$longitude,'$location'); ";
////echo "<br/>TRACE $sql";//TRACE
//            $idBooth = $APP_BdD->ExecuteInsert($sql); 
//            if($idBooth){
//                $sql = "INSERT INTO  `App_boothDongle` (`idBooth`, `idDongle`) VALUES ($idBooth,1); ";
////char to bigint                $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
//            }
//            else { echo "KO";  }
//        }
//        else{//update
////char to bigint            $sql = "UPDATE  `App_booths` SET `name`='$name', `latitude`='$latitude', `longitude`='$longitude', `location`='$location' WHERE `idBooth`=$idBooth; ";
//            $sql = "UPDATE  `App_booths` SET `name`='$name', `latitude`=$latitude, `longitude`=$longitude, `location`='$location' WHERE `idBooth`=$idBooth; ";
//            $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
//        }
//        break;
//    case 3://cambio de estado info alerta offline para un booth
//        if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];} else return;
//        if(!$idBooth) return;
//        if(isset($_REQUEST['checkOffline'])){ $checkOffline = $_REQUEST['checkOffline'];} else $checkOffline = 0;
//        if(isset($_REQUEST['hS'])){ $hS = $_REQUEST['hS'];} else return;
//        if(isset($_REQUEST['mS'])){ $mS = $_REQUEST['mS'];} else return;
//        if(isset($_REQUEST['hE'])){ $hE = $_REQUEST['hE'];} else return;
//        if(isset($_REQUEST['mE'])){ $mE = $_REQUEST['mE'];} else return;
//        $sql = "UPDATE  `App_booths` SET `alertOffline`=$checkOffline, `hS`=$hS, `mS`=$mS, `hE`=$hE, `mE`=$mE WHERE `idBooth`=$idBooth; ";
////echo "<br/>TRACE $sql";//TRACE
//        $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
//       break;
//    case 4://cambio de estado offline/online de un booth
//        if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];} else return;
//        if(!$idBooth) return;
//        if(isset($_REQUEST['setState'])){ $setState = $_REQUEST['setState'];} else return;
//        //busco una alerta offline sense solventar ???????
//        //SELECT `id`, `idBooth`, `typeAlert`, `when`, `estat` FROM `App_boothAlert` WHERE 1
//      //$sql = "SELECT `id` FROM `App_boothAlert` WHERE `idBooth`=$idBooth AND `estat`<2 ORDER BY `when` DESC; ";
//
//        $sql = "UPDATE `App_boothAlert` SET estat=2 WHERE `idBooth`=$idBooth AND `typeAlert`=1; ";
////echo "<br/>TRACE $sql";//TRACE
//        if($setState == 'offline'){
//            $ara = new DateTime("now");
//            $sql = "INSERT INTO `App_boothAlert` (`idBooth`, `typeAlert`, `when`, `estat`) VALUES ($idBooth,1,{$APP_BdD->myDateTimeSerial($ara)},0); ";
////echo "<br/>TRACE $sql";//TRACE
//            $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
//        }
//        
//        echo "estado: ". my_checkBoothAlerts($idBooth,$APP_BdD);
//        break;
//
//    case 5://cambio de estado alerta money de un booth
//        if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];} else return;
//        if(!$idBooth) return;
//        if(isset($_REQUEST['setState'])){ $setState = $_REQUEST['setState'];} else return;
//
//        $sql = "UPDATE `App_boothAlert` SET estat=2 WHERE `idBooth`=$idBooth AND `typeAlert`=12; ";
////echo "<br/>TRACE $sql";//TRACE
//        if($setState == 'alerta'){
//            $ara = new DateTime("now");
//            $sql = "INSERT INTO `App_boothAlert` (`idBooth`, `typeAlert`, `when`, `estat`) VALUES ($idBooth,12,{$APP_BdD->myDateTimeSerial($ara)},0); ";
////echo "<br/>TRACE $sql";//TRACE
//            $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
//        }
//
//        echo "estado: ". my_checkBoothAlerts($idBooth,$APP_BdD);
//        break;
//
//    case 6://cambio de estado alerta film de un booth
//        if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];} else return;
//        if(!$idBooth) return;
//        if(isset($_REQUEST['setState'])){ $setState = $_REQUEST['setState'];} else return;
//
//        $sql = "UPDATE `App_boothAlert` SET estat=2 WHERE `idBooth`=$idBooth AND `typeAlert`=11; ";
////echo "<br/>TRACE $sql";//TRACE
//        if($setState == 'alerta'){
//            $ara = new DateTime("now");
//            $sql = "INSERT INTO `App_boothAlert` (`idBooth`, `typeAlert`, `when`, `estat`) VALUES ($idBooth,11,{$APP_BdD->myDateTimeSerial($ara)},0); ";
////echo "<br/>TRACE $sql";//TRACE
//            $esOK = $APP_BdD->Execute($sql); if($esOK){ echo "OK"; } else{ echo "KO";  }
//        }
//
//        echo "estado: ". my_checkBoothAlerts($idBooth,$APP_BdD);
//        break;

}

function my_checkBoothAlerts($idBooth,$APP_BdD){
$sql = "SELECT * FROM `App_boothAlert` WHERE `idBooth`=$idBooth AND `estat`<2; ";


$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "Error $sql";
return;
}
$estat = 0;
if($APP_BdD->FetchRs()){
    $estat = 1;
}
$APP_BdD->CloseRs();

//modifiquuem estat
$sql = "UPDATE  `App_booths` SET `estat`=$estat WHERE `idBooth`=$idBooth; ";
//echo "TRACE 01 $sql";
//return 3;
$esOK = $APP_BdD->Execute($sql); if(!$esOK){ echo "Error $sql";  }


return $estat;
}



?>
