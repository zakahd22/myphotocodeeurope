<?php
$APP_common_no_idb = true;//20140626
require("common.php");
//header('Content-Type: image/jpeg');


if(!$APP_dongleOK) return;



if(isset($_REQUEST['UPGRADEid'])){ $UPGRADEid = $_REQUEST['UPGRADEid'];}
else{
echo "Error - UPGRADEid param";
return;
}

//202301 upgrade INICI
$upg401nomFitxerExe = "";
switch($UPGRADEid){
    case "upgB401ng9550":
        $upg401nomFitxerExe = "NG-9550.exe";
        include("../../PB/upg401.php");
        return;
    case "upgB401ng9810":
        $upg401nomFitxerExe = "NG-9810.exe";
        include("../../PB/upg401.php");
        return;
    case "upgB401ngD80":
        $upg401nomFitxerExe = "NG-D80.exe";
        include("../../PB/upg401.php");
        return;
    case "upgB401ngD90":
        $upg401nomFitxerExe = "NG-D90.exe";
        include("../../PB/upg401.php");
        return;
    case "upgB401ngRX1":
        $upg401nomFitxerExe = "NG-RX1.exe";
        include("../../PB/upg401.php");
        return;
}

//202301 upgrade FINAL


//20220707 upgrade 3.3
$upg3030000nomFitxerExe = "";
switch($UPGRADEid){
    case "upg3030000ng9550":
        $upg3030000nomFitxerExe = "NG-9550.exe";
        include("../../PB/upg3030000.php");
        return;
    case "upg3030000ng9810":
        $upg3030000nomFitxerExe = "NG-9810.exe";
        include("../../PB/upg3030000.php"); 
        return;
    case "upg3030000ngD80":
        $upg3030000nomFitxerExe = "NG-D80.exe";
        include("../../PB/upg3030000.php"); 
        return;
    case "upg3030000ngD90":
        $upg3030000nomFitxerExe = "NG-D90.exe";
        include("../../PB/upg3030000.php"); 
        return;   
    case "upg3030000ngRX1":
        $upg3030000nomFitxerExe = "NG-RX1.exe";
        include("../../PB/upg3030000.php"); 
        return;    
}

//20220621 upgrade 3.2
$upB3020000nomFitxerExe = "";
switch($UPGRADEid){
    case "upg3020000ng9550":
        $upB3020000nomFitxerExe = "NG-9550.exe";
        include("../../PB/upB3020000.php");
        return;
    case "upg3020000ng9810":
        $upB3020000nomFitxerExe = "NG-9810.exe";
        include("../../PB/upB3020000.php"); 
        return;
    case "upg3020000ngD80":
        $upB3020000nomFitxerExe = "NG-D80.exe";
        include("../../PB/upB3020000.php"); 
        return;
    case "upg3020000ngD90":
        $upB3020000nomFitxerExe = "NG-D90.exe";
        include("../../PB/upB3020000.php"); 
        return;   
    case "upg3020000ngRX1":
        $upB3020000nomFitxerExe = "NG-RX1.exe";
        include("../../PB/upB3020000.php"); 
        return;    
}


//#Britta303upgrade 2
//20220502 upgrade 3.1.6.5
$upB3010605nomFitxerExe = "";
switch($UPGRADEid){
    case "upB3010605ng9550":
        $upB3010605nomFitxerExe = "NG-9550.exe";
        include("../../PB/upB3010605.php");
        return;
    case "upB3010605ng9810":
        $upB3010605nomFitxerExe = "NG-9810.exe";
        include("../../PB/upB3010605.php"); 
        return;
    case "upB3010605ngD80":
        $upB3010605nomFitxerExe = "NG-D80.exe";
        include("../../PB/upB3010605.php"); 
        return;
}


//#Britta316upgrade
//comento el codi anterior, començarem amb upgrade de 3.1.6. Els PBs enviaran UPGRADEid B3.1.5 !!! Cal comprovar bootDC per a discriminar

$upB316nomFitxerExe = "";
switch($UPGRADEid){
    case "upB316ng9550":
        $upB316nomFitxerExe = "NG-9550.exe";
        include("../../PB/upB316.php");
        return;
    case "upB316ng9810":
        $upB316nomFitxerExe = "NG-9810.exe";
        include("../../PB/upB316.php"); 
        return;
    case "upB316ngD80":
        $upB316nomFitxerExe = "NG-D80.exe";
        include("../../PB/upB316.php"); 
        return;
    case "upB316ngRX1":
        $upB316nomFitxerExe = "NG-RX1.exe";
        include("../../PB/upB316.php"); 
        return;
//    case "":
//        break;
}






//if($nomFitxer){
//    $fp = fopen($nomFitxer, "r");
//    fpassthru($fp);
//    fclose($fp);
//}

//$tipusPB = substr($idmaq, 0, 1);
//	//A Strip sense def
//	//B Wall (P12)
//	//C MegaIn ()
//	//D MegaOut (KG4viewer fins 04/03/2012; despres (OUT)
//	//E Party (PNG)
//	//F NewGeneration (NG)
//	//G IPS
//	//H Igo (IGO)
//	//J MegaOutMail (saltem la I)
//	//K Arena (un altre projecte!!!!!!)
//	//L NG2P
//	//M MEGA2P
//	//N PUBLI
//
//
//
//switch($UPGRADEid){
////    case "2a":
////        echo "ok#20";
////        return;
//    case "20": //ara faré el download d'un nou exe
//        include("../../PB/up20.php");
////        $fp = fopen("../../PB/up20/$tipusPB/newexe.20", "r");
////           fpassthru($fp);
////           fclose($fp);
//
//        return;
//    case "21": //un nou exe i, per a proves, un cab
//        include("../../PB/up21.php");
//        return;
//        
////201502 INICI        
//    case "N01":
//        include("../../PB/upN01.php");
//        return;
////201502 FINAL        
//        
//        
////201502 INICI        
//    case "N0B":
//        include("../../PB/upN0B.php");
//        return;
////201502 FINAL        
//}


echo "Error -Invalid UPGRADEid param";


?>
