<?php
require_once dirname(__FILE__). '/../common/global.php';
//Entrada exterior QR PANIC INSTUCCTIONS!!
if(isset($_REQUEST['code'])){
    $code = $_REQUEST['code'];
    $p = $_REQUEST['p'];
    $question = $_REQUEST['q'];

    $QS = mysql_query("SELECT x.serialnumber , x.owner , x.CLD_idType , x.type FROM App_booths x WHERE x.idBooth=$p");
    if($r = mysql_fetch_array($QS)){
        $serialnumber = $r[0];
        $owner = $r[1];
        $idboothType = $r[2];
        $boothType = $r[3];
        $code2= md5($p."#".$serialnumber."#".$owner);   
        if($code2==$code){
            $dataTiempo = date("Y-m-d G:i:s");
            mysql_query("INSERT INTO SAT_problems (propietari , solved , booth_id , boothType , dataTiempo) VALUES($owner , 0 , $p,'$boothType' , '$dataTiempo')");
            $_SESSION['enquesta'] = mysql_insert_id();
            $_SESSION['Q_EXT']= $question;
            $_SESSION['USERID'] = $owner;
            $_SESSION['USERTYPE'] = 4;
            $_SESSION['boothID']= $p;
            $_SESSION['boothType'] = $idboothType;
            $qq = mysql_query("SELECT name FROM rentals WHERE id=$owner");
            if($r2 = mysql_fetch_array($qq)){
                $_SESSION['USERNAME'] = $r2[0];
            }
        }
    }
}
//Fi entrada Exterior QR PANIC INSTUCCTIONS!!
/*
if(!(isset($_SESSION['USERTYPE'])) OR empty($_SESSION['USERTYPE'])){
    header( "Location: ./index.php?error=1" ) ;
}
*/
$userType = $_SESSION['USERTYPE'];

if($userType==1){
    $_SESSION['USERID']= 9999991;
}

//$_SESSION['USERID']= 9999991;
?>