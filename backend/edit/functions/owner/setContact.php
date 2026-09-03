<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$name = $_POST['name'];
$lastname = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$job = $_POST['job'];
$timezone = $_POST['timezone'];
$ID = $_POST['id'];
if (empty($name) || empty($lastname) || empty($email) || empty($phone) || empty($timezone)) {
            echo "All Fields are required.";
        } else {            
            $lastname = addslashes($lastname);
            $name = addslashes($name);
            $job = addslashes($job);
              
            if($CLD_CON->Execute("UPDATE CLD_Contactes SET name='$name' , surnames = '$lastname' ,  phone= '$phone'  , email='$email' , city='$timezone' , carrec='$job' WHERE id=$ID")){
                echo "OK";
            }else{
                echo "Have been a error , please try again";
            }
        }
?>