<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

$ID = $_POST['id'];

$c=1;
$CLD_CON2 = clone($CLD_CON);

echo "<div class='inContent'>";
echo "<h1>Bussiness Addresses <input type='button' class='miniAdd' onclick='edit(34 , $ID);'></h1>";
$CLD_CON->OpenRs("SELECT * FROM App_ownerAddress WHERE idOwner=$ID AND CLD_type=1 ORDER BY id");
if($CLD_CON->GetRsRows() ==0 ){
    echo "<p>If there is a business address, add it here.</p>";
}
while ($CLD_CON->FetchArray()){
    $address_id = $CLD_CON->GetArrayField("id");
    $address = $CLD_CON->GetArrayField("address");
    $state = $CLD_CON->GetArrayField("state");
    $zipCode = $CLD_CON->GetArrayField("code");
    $country = $CLD_CON->GetArrayField("country");
    $city = $CLD_CON->GetArrayField("city");
    $preference = $CLD_CON->GetArrayField("preference");
     $contact = $CLD_CON->GetArrayField("CLD_contactName");
    $company = $CLD_CON->GetArrayField("CLD_companyName");
    $phone = $CLD_CON->GetArrayField("CLD_phone");
    if(empty($contact)){
        $contact = "No Contact Name";
    }
    if(empty($company)){
        $company = "No Company Name";
    }
    if(empty($phone)){
        $phone = "No Phone";
    }

    echo "<div class='address1'>";
    echo "<p>$company <span style='margin-right:10%;display:inline;float:right;'>$contact - $phone</span></p>";
    echo "<hr style='height:2px;background-color:white;margin-top:3px; margin-bottom:5px;'>";
    echo "<p> $address</p>";
    echo "<p> ZIP CODE : $zipCode</p>";
    echo "<p> City : $city</p><p>State : $state </p>";
    echo "<p> Country :  $country</p>";
    echo "<input type='button' class='editButton' style='float:right;bottom:35px;' onClick='edit(5 , $address_id);'>";
    echo "<input type='button' class='miniTrash' style='float:right;bottom:32px;' onClick='deleteAddress($address_id);'>";
    if($preference==1){
        echo "<img src='images/web/preferenceOK.png' style='position:absolute; top:10px;right:10px;width:36px;height:36px;' title='PRIMARY ADDRESS'>";
    }
    else{
        echo "<img src='images/web/preferenceNoOk.png' style='position:absolute; top:10px;right:10px;cursor:pointer;' onclick='setToPrimary($address_id , $preference , $ID , 1);' title='SET TO PRIMARY ADDRESS'>";
    }
    echo "</div>";
    $c++;
}

$c=1;
echo "<h1>Shipping Address <input type='button' class='miniAdd' onclick='edit(35 , $ID);'></h1>";
$CLD_CON2->OpenRs("SELECT * FROM App_ownerAddress WHERE idOwner=$ID AND CLD_type=0 ORDER BY preference");
if($CLD_CON2->GetRsRows() ==0 ){
    echo "<p>If there is a shipping address, add it here.</p>";    
}
while ($CLD_CON2->FetchArray()){
    $address_id = $CLD_CON2->GetArrayField("id");
    $address = $CLD_CON2->GetArrayField("address");
    $state = $CLD_CON2->GetArrayField("state");
    $zipCode = $CLD_CON2->GetArrayField("code");
    $country = $CLD_CON2->GetArrayField("country");
    $city = $CLD_CON2->GetArrayField("city");   
    $preference = $CLD_CON2->GetArrayField("preference");
    $contact = $CLD_CON2->GetArrayField("CLD_contactName");
    $company = $CLD_CON2->GetArrayField("CLD_companyName");
    $phone = $CLD_CON2->GetArrayField("CLD_phone");
    
    if(empty($contact)){
        $contact = "No Contact Name";
    }
    if(empty($company)){
        $company = "No Company Name";
    }
    if(empty($phone)){
        $phone = "No Phone";
    }
    
    echo "<div class='address2'>";
    echo "<p>$company     <span style='margin-right:10%;display:inline;float:right;'>$contact - $phone</span></p>";
    echo "<hr style='height:2px;background-color:white;margin-top:3px; margin-bottom:5px;'>";
    echo "<p> $address</p>";
    echo "<p> ZIP CODE : $zipCode</p>";
    echo "<p> City : $city</p><p>State : $state </p>";
    echo "<p> Country :  $country</p>";
    echo "<input type='button' class='editButton' style='float:right;bottom:35px;' onClick='edit(70 , $address_id);'>";
    echo "<input type='button' class='miniTrash' style='float:right;bottom:32px;' onClick='deleteAddress($address_id);'>";
    if($preference==1){
        echo "<img src='images/web/preferenceOK.png' style='position:absolute; top:10px;right:10px;width:36px;height:36px;' title='PRIMARY ADDRESS'>";
    }
    else{
        echo "<img src='images/web/preferenceNoOk.png' style='position:absolute; top:10px;right:10px;cursor:pointer;' onclick='setToPrimary($address_id , $preference , $ID , 0);' title='SET TO PRIMARY ADDRESS'>";
    }
    echo "</div>";

    $c++;
}
echo "</div>";

?>
<script> 
    function deleteAddress(id){

       if(confirm("You are sure you want to delete this address?")){
                var ajaxData = {id: id};
                $.ajax({
                    url: 'sections/owner/functions/deleteAddress.php',
                    type: 'POST',
                    success: function(data) {
                        if (data === "OK") {                           
                            closePopup();
                            profile("owner", "addresses", <?php echo $ID;?>);
                        } 
                        else{
                            alert(data);
                        }
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
       }
    }
    function setToPrimary(id , p , o , t){
        var ajaxData = {id: id , preference : p , owner : o , type : t};
        $.ajax({
            url: 'sections/owner/functions/setToPrimaryAddress.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {                           
                    profile("owner", "addresses", o);
                } 
                else {
                    alert(data);
                }
            },
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
</script>