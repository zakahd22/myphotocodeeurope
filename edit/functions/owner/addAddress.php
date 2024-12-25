<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$street = $_POST['street'];
$city = $_POST['city'];
$country = $_POST['country'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$type = $_POST['type'];
$owner = $_POST['own'];
$company = $_POST['company'];
$phone = $_POST['phone'];
$contact = $_POST['contact'];

if (empty($company) || empty($street) || empty($city) || empty($zip) || empty($country) || empty($contact) || empty($phone)) {
    echo "Please fill all the required fields (*)";
} 
else {            
    $street = addslashes($street);
    $city = addslashes($city);
    $country = addslashes($country);
    $state = addslashes($state);
    $zip = addslashes($zip);
    $company = addslashes($company);
    $phone = addslashes($phone);
    $contact = addslashes($contact);

    if($CLD_CON->Execute("INSERT INTO App_ownerAddress (idOwner , preference , address ,city , state , code , country , CLD_status , CLD_type , CLD_companyName , CLD_contactName , CLD_phone) VALUES($owner , 20 , '$street' , '$city' , '$state' , '$zip' , '$country' , 0 , $type , '$company' , '$contact' , '$phone' )")){
        echo "OK";
    }
    else{
        echo "Error has ocurred , please try again later";
    }
}
?>
