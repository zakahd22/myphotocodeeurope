<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$street = $_POST['street'];
$city = $_POST['city'];
$country = $_POST['country'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$company = $_POST['company'];
$phone = $_POST['phone'];
$contact = $_POST['contact'];
//echo $zip . " "  . $city . " " . $country . " " . $state . " " . $street;
$CLD_CON->OpenRs("SELECT * FROM App_ownerAddress WHERE id= $ID");
if ($CLD_CON->FetchArray()) {
    $street2 = $CLD_CON->GetArrayField("address");
    $state2 = $CLD_CON->GetArrayField("state");
    $zip2 = $CLD_CON->GetArrayField("code");
    $country2 = $CLD_CON->GetArrayField("country");
    $city2 = $CLD_CON->GetArrayField("city");
    $contact2 = $CLD_CON->GetArrayField("CLD_contactName");
    $company2 = $CLD_CON->GetArrayField("CLD_companyName");
    $phone2 = $CLD_CON->GetArrayField("CLD_phone");
    
    if ($street == $street2 && $zip == $zip2 && $country == $country2 && $city == $city2 && $state == $state2 && $phone==$phone2 && $company == $company2 && $contact == $contact2) {
        echo "OK";
    }
    else{
        if (empty($street) || empty($zip) || empty($state) || empty($city) || empty($country) || empty($contact) || empty($company) || empty($phone)) {
            echo "All Fields are required.";
        } 
        else {  
//            $company = $CLD_CON->prepareString($company);
//            $street = $CLD_CON->prepareString($street);
//            $city = $CLD_CON->prepareString($city);
//            $country = $CLD_CON->prepareString($country);
//            $state = $CLD_CON->prepareString($state);
//            $zip = $CLD_CON->prepareString($zip);
            
            if($CLD_CON->Execute("UPDATE App_ownerAddress SET address='$street' , country='$country' , state='$state' , code='$zip' , city='$city' , CLD_status=0 , CLD_contactName = '$contact'  , CLD_companyName = '$company' , CLD_phone= '$phone' WHERE id=$ID")){
                echo "OK";
            }
            else{
                echo "Have been a error, please try again";
            }
        }
    }
}

    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */
?>
