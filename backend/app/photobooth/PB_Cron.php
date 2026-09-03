<?php


$fp = fopen('data.txt', 'w');
fwrite($fp, '1');
fwrite($fp, '23');
fwrite($fp, '\r');
fclose($fp);


//require("../common/APP_BdD.php");
//'db399687929.db.1and1.com','dbo399687929','digitalcentre','db399687929'
$myConn = mysql_connect('db399687929.db.1and1.com','dbo399687929','digitalcentre');

    if (!$myConn){
        
        
$fp = fopen('data.txt', 'a');
fwrite($fp, 'Error Connecting to host\r');
fclose($fp);
        
        
        return;
    }
    
$fp = fopen('data.txt', 'a');
fwrite($fp, 'ok Connection to host\r');
fclose($fp);

if (!mysql_select_db('db399687929', $myConn)){
    
    
        
$fp = fopen('data.txt', 'a');
fwrite($fp, 'Error opening db\r');
fclose($fp);
    
    
    return;
}    
    
    
    
//ara fem una prova

$sql = "UPDATE App_booths SET serialnumber='1234' WHERE idBooth = 5";
if(!mysql_query($sql)){
//    echo "Error - Common checkAlerts - code 02 $sql.";
//    $APP_common_error = true;
//    return;

}



?>
