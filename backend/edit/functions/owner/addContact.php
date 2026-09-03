<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$name = $_POST['name'];
$lastname = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$job = $_POST['job'];
$timezone = $_POST['timezone'];
$owner = $_POST['owner'];
$CLD_CON->OpenRs("SELECT * FROM CLD_Contactes WHERE rental_id=$owner");
$preference = $CLD_CON->GetRsRows() + 1;
if (empty($name) || empty($email) || empty($phone) || empty($timezone)) {
            echo "All Fields are required.";
        } else {            
            $lastname = addslashes($lastname);
            $name = addslashes($name);
            $job = addslashes($job);              
            if($CLD_CON->Execute("INSERT INTO CLD_Contactes (preference , name , surnames ,  phone ,email , city , rental_id , carrec)".
                    " VALUES($preference , '$name' , '$lastname' , '$phone' , '$email' , '$timezone' , $owner , '$job' )")){
                echo "OK";
            }else{
                echo "Have been a error , please try again";
            }
        }
     
?>